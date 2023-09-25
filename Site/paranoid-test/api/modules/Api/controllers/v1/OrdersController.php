<?php

namespace app\api\modules\Api\controllers\v1;

use app\api\modules\Api\Repositories\v1\OrdersRepository;
use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Default controller for the `api` module
 */
class OrdersController extends RestController
{

    private string $repository = OrdersRepository::class;

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
    public function actionCheckout(Request $request): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'POST'))
            return $methodNotExist;

        $result  = (new $this->repository)->checkout();

        return $this->JSONResponse($result, $result['status']);
    }


    /**
     * @throws InvalidConfigException
     */
    public function actionUpdate(Request $request, $id): array|object
    {
        if ($methodNotExist = $this->checkMethod($request, 'PUT'))
            return $methodNotExist;

        $result  = (new $this->repository)->update($id);

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
