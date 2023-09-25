<?php

namespace app\api\modules\Api\controllers\v1;

use yii\base\InvalidConfigException;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\Response;

abstract class RestController extends Controller
{
    /**
     * @throws InvalidConfigException
     */
    protected function JSONResponse($data, $statusCode): object
    {
        if (is_bool($statusCode))
            $statusCode = $statusCode ? 200 : 500;

        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'statusCode'=> $statusCode,
            'data' => $data,
        ]);
    }

    /**
     * @throws InvalidConfigException
     */
    protected function checkMethod(Request $request, $method): array|object|null
    {
        if ($request->method !== $method)
            return $this->JSONResponse([
                'status' => false,
                'message' => 'Method Not Allowed',
            ], 405);

        return null;
    }

}