<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Join Us';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'sponsor')->textInput(['autofocus' => true]) ?>
                </div>
                
            </div>
            <div class="row">
                <div class="col-sm-12">
                <?= $form->field($model, 'email') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'email_repeat') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                <?= $form->field($model, 'password')->passwordInput() ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'password_repeat')->passwordInput() ?>
                </div>
            </div>
                <div class="form-group">
                    Clicking here below means that you agree with our <?= Html::a('Terms and Conditions', ['termsandconditions'])?><br>
                    <?= Html::submitButton('Join', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
