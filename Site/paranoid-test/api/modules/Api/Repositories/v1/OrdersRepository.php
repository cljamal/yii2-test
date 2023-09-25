<?php

namespace app\api\modules\Api\Repositories\v1;

use app\api\modules\Api\models\v1\Customer;
use app\api\modules\Api\models\v1\Order;
use app\api\modules\Api\models\v1\Product;
use yii\data\Pagination;
use yii\web\Request;

class OrdersRepository
{
    private int $defaultPageSize = 10;

    private ?Order $model = null;

    private function model(): Order
    {
        if (!$this->model)
            $this->model = new Order();

        return $this->model;
    }

    protected function redis()
    {
        return \Yii::$app->cache->redis;
    }

    public function checkout(): array
    {
        $data = \Yii::$app->request->post();
        if (!count($data))
            $data = json_decode(\Yii::$app->request->getRawBody(), true);

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            return [
                'status' => false,
                'message' => [
                    'Email is not valid'
                ]
            ];

        $customer = (new CustomersRepository)->getByEmail($data['email']);
        if (!$customer){
            return [
                'status' => false,
                'message' => [
                    'Customer not found'
                ]
            ];

            /**
             * Or we can create new Customer by modify new customer
             * \Yii::$app->request->setBodyParams(['email' => $data['email']]);
             * $customer = (new CustomersRepository)->create(\Yii::$app->request);
             */
        }

        if (!isset($data['order_info']))
            return [
                'status' => false,
                'message' => [
                    'Order info is empty'
                ]
            ];

        $products = [];
        $orderInfo = [];
        $total = 0;
        foreach ($data['order_info'] as $info)
        {
            $product = Product::findOne($info['product_id']);
            if (!$product)
                return [
                    'status' => false,
                    'message' => [
                        'Product not found'
                    ]
                ];

            if ($product->in_stock < $info['qty'])
                return [
                    'status' => false,
                    'message' => [
                        'Product: '. $product->name .' not enough in stock'
                    ]
                ];

            $orderInfo[] = [
                'product_id' => $info['product_id'],
                'product_name' => $product->name,
                'price' => $product->price,
                'qty' => $info['qty'],
            ];

            $total += $product->price * $info['qty'];

            $product->in_stock = $product->in_stock - $info['qty'];
            $products[] = $product;
        }

        $orderData = [
            'customer_id' => $customer->id,
            'order_info' => $orderInfo,
            'total_price' => $total,
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];

        $order = new Order();
        $order->attributes = $orderData;
        if ($order->validate())
        {
            $order->save();
            foreach ($products as $product)
                $product->save();

            return [
                'status' => true,
                'message' => $order->toArray()
            ];
        }

        return [
            'status' => false,
            'orderData' => $orderData,
            'message' => $order->errors
        ];
    }

    private function preparePostData(): array
    {
        $result = [];
        $data = json_decode(\Yii::$app->request->getRawBody(), true);

        if (!count($data))
            return $result;

        foreach ($data as $key => $value) {
            $value = match ($key) {
                'id' => $value = (int) trim($value),
                'order_info' => $value,
                'total_price' => $value = (int) trim($value),
                'status' => $value = trim($value),
                default => $value = null
            };

            if($value)
                $result[$key] = $value;
        }

        if (!isset($result['created_at']))
            $result['created_at'] = date('Y-m-d', time());

        $result['updated_at'] = date('Y-m-d', time());

        return $result;
    }

    public function update($id): array
    {
        if ($model = $this->model()->findOne($id))
        {
            $data = $this->preparePostData();
            unset($data['email']);
            unset($data['created_at']);
            $model->attributes = $data;
            if ($model->validate())
            {
                $model->save();
                return [
                    'status' => true,
                    'message' => $model->toArray()
                ];
            }else{
                return [
                    'status' => false,
                    'message' => $model->errors
                ];
            }
        }

        return [
            'status' => false,
            'message' => [
                'Order not found'
            ]
        ];
    }

    public function delete($id): array
    {

        if ($model = $this->model()->findOne($id))
        {
            $deletedModel = clone $model;
            $model->delete();
            return [
                'status' => true,
                'message' => 'Order with id ' . $deletedModel->id . ' was deleted'
            ];
        }

        return [
            'status' => false,
            'message' => [
                'Order not found'
            ]
        ];
    }

    public function show($id): array
    {

        if ($model = $this->model()->findOne($id))
        {
            return [
                'status' => true,
                'message' => $model->toArray()
            ];
        }

        return [
            'status' => false,
            'message' => [
                'Order not found'
            ]
        ];
    }

    public function get(): array
    {
        $query = $this->model()::find();

        $pages = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => $this->defaultPageSize,
            'defaultPageSize' => $this->defaultPageSize,
            'forcePageParam' => false,
            'pageSizeParam' => false,
        ]);

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return [
            'status' => true,
            'message' => [
                'items' => $models,
                'pages' => [
                    'itemsTotal' => $pages->totalCount,
                    'last' => $pages->getPageCount(),
                    'currentPage' => $pages->getPage() + 1,
                    'perPage' => $pages->getPageSize(),
                ],
            ]
        ];
    }

    public function create(Request $request): array
    {
        $this->model()->load($this->preparePostData(), '');

        if (!$this->model()->validate())
            return [
                'status' => false,
                'message' => $this->model()->errors
            ];

        $this->model()->save();

        return [
            'status' => true,
            'message' => $this->model()->toArray()
        ];
    }
}