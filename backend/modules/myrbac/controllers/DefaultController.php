<?php

namespace backend\modules\myrbac\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller for the `myrbac` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest)$this->redirect (['/switchboard/index']);
        return $this->render('index');
    }
}
