<?php

namespace app\api\modules\Api;

/**
 * api-v1 module definition class
 */
class REST extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\api\modules\Api\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}
