<?php

namespace app\api\modules\Api\Repositories\v1;

use app\api\modules\Api\models\v1\Customer;
use app\api\modules\Api\models\v1\Product;
use Throwable;
use yii\data\Pagination;
use yii\db\StaleObjectException;
use yii\web\Request;

class ProductsRepository
{
    private int $defaultPageSize = 10;

    private ?Product $model = null;

    private function model(): Product
    {
        if (!$this->model)
            $this->model = new Product();

        return $this->model;
    }

    private function preparePostData($data): array
    {
        $result = [];
        if (!count($data))
            $data = json_decode(\Yii::$app->request->getRawBody(), true);

        if (!count($data))
            return $result;

        foreach ($data as $key => $value) {
            $value = match ($key) {
                'id' => $value = (int) trim($value),
                'name', 'description' => $value = trim($value),
                'price' => $value = (int) trim($value),
                'in_stock' => $value = (int) trim($value),
            };

            $result[$key] = $value;
        }
        if (!isset($result['created_at']))
            $result['created_at'] = date('Y-m-d', time());

        if (!isset($result['updated_at']))
            $result['updated_at'] = date('Y-m-d', time());

        return $result;
    }

    public function update($id, $data): array
    {
        if ($model = $this->model()->findOne($id))
        {
            $data = $this->preparePostData($data);
            $model->attributes = $data;
            if ($model->validate())
            {
                $model->save();
                return [
                    'status' => true,
                    'message' => $model->toArray()
                ];
            }
        }

        return [
            'status' => false,
            'message' => [
                'Product not found'
            ]
        ];
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function delete($id): array
    {

        if ($model = $this->model()->findOne($id))
        {
            $deletedModel = clone $model;
            $model->delete();
            return [
                'status' => true,
                'message' => 'Product with id ' . $deletedModel->id . ' was deleted'
            ];
        }

        return [
            'status' => false,
            'message' => [
                'Product not found'
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
                'Product not found'
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
        $this->model()->load($this->preparePostData($request->post()), '');

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