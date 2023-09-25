<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Request;

class SayHeloController extends Controller
{
    public function actionIndex(Request $request)
    {
        dd($request->queryParams);
        return $this->render('index');
    }
}
