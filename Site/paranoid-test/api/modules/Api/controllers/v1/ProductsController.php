<?php

namespace app\api\modules\Api\controllers\v1;

use app\api\modules\Api\Repositories\v1\ProductsRepository;
use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Default controller for the `api` module
 */
class ProductsController extends RestController
{
    private string $repository = ProductsRepository::class;

    /**
     * @throws InvalidConfigException
     */
    public function actionIndex(Request $request): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'GET'))
            return $methodNotExist;

        $result  = (new $this->repository)->get();

        return $this->JSONResponse($result, $result['status']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionCreate(Request $request): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'POST'))
            return $methodNotExist;

        $result  = (new $this->repository)->create(\Yii::$app->request);

        return $this->JSONResponse($result, $result['status']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionUpdate(Request $request, $id): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'PUT'))
            return $methodNotExist;

        $result  = (new $this->repository)->update($id, $request->post());

        return $this->JSONResponse($result, $result['status']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionShow(Request $request, $id): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'GET'))
            return $methodNotExist;

        $result  = (new $this->repository)->show($id);

        return $this->JSONResponse($result, $result['status']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionDelete(Request $request, $id): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'DELETE'))
            return $methodNotExist;

        $result  = (new $this->repository)->delete($id);

        return $this->JSONResponse($result, $result['status']);
    }
}
