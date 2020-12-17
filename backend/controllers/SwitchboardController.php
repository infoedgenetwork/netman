<?php

namespace backend\controllers;

use Yii;

class SwitchboardController extends \yii\web\Controller
{
    
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest){
            $this->redirect(['/site/login']);
        }
        return $this->render('index');
    }

}
