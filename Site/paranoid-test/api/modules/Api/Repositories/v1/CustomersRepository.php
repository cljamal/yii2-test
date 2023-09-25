<?php

namespace app\api\modules\Api\Repositories\v1;

use app\api\modules\Api\models\v1\Customer;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\web\Request;

class CustomersRepository
{
    private int $defaultPageSize = 10;

    private ?Customer $model = null;

    private function model(): Customer
    {
        if (!$this->model)
            $this->model = new Customer();

        return $this->model;
    }

    private function preparePostData($data): array
    {
        $result = [];

        $jsonSentData = json_decode(\Yii::$app->request->getRawBody(), true );
        if ($jsonSentData)
            $data = $jsonSentData;

        if (!count($data))
            return $result;

        foreach ($data as $key => $value) {
            $value = match ($key) {
                'id' => $value = (int) trim($value),
                'first_name', 'last_name', 'email' => $value = trim($value),
                'dob' => $value = date('Y-m-d', strtotime($value)),
            };

            $result[$key] = $value;
        }

        return $result;
    }

    public function update($id, $data): array
    {
        $jsonSentData = json_decode(\Yii::$app->request->getRawBody(), true );
        if ($jsonSentData)
            $data = $jsonSentData;

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
                'Customer not found'
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
                'message' => 'Customer with id ' . $deletedModel->id . ' was deleted'
            ];
        }

        return [
            'status' => false,
            'message' => [
                'Customer not found'
            ]
        ];
    }

    public function show($id): array
    {

        if ($model = $this->model()->find()->where(['id' => $id])->with('orders')->one())
        {
            return [
                'status' => true,
                'data' => [
                    ...$model,
                    'orders' => $model->orders
                ]
            ];
        }

        return [
            'status' => false,
            'message' => [
                'Customer not found'
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

    public function getByEmail($email): Customer|ActiveRecord
    {
        return $this->model()->find()->where(['email' => $email])->one();
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