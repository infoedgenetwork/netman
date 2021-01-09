<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use backend\modules\payments\models\Paymenttypes;
use backend\modules\payments\models\Paymethods;
use backend\modules\payments\models\Sponsorship;
use backend\modules\payments\models\Packages;
use backend\modules\basic\models\People;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Inpayments */
/* @var $form ActiveForm */
$this->title = Yii::t('app', 'Confirm Payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Config'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Check Payments'), 'url' => ['checkpay']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="inpayments-confirmpay">

    <?php $form = ActiveForm::begin(); ?>
    <h1><?= Html::encode($this->title) ?></h1>
        <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'member')->dropDownList(ArrayHelper::map(People::find()->all(), 'id', 'FullName'), ['disabled' => 'disabled']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ptype')->dropDownList(ArrayHelper::map(Paymenttypes::find()->all(), 'id', 'ptypeName'), ['disabled' => 'disabled']) ?>
        </div>




    </div>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'package')->dropDownList(ArrayHelper::map(Packages::find()->all(), 'id', 'packName'),['prompt'=> '--Select preferred Package--', 'disabled' => 'disabled']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'pMethod')->dropDownList(ArrayHelper::map(Paymethods::find()->all(), 'id', 'methodName'),['prompt'=>'--Select payment method--', 'disabled' => 'disabled']) ?>
        </div>
        
        <div class="col-sm-3">
            <?= $form->field($model, 'transactionNo')->textInput(['maxlength' => true , 'disabled' => 'disabled']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'amount')->textInput(['disabled' => 'disabled'] ) ?>
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-offset-1 col-sm-3">
            <?= $form->field($model, 'confirmed')->radioList( [1=>'Yes',0=>'No']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'comments')->textarea( ) ?>
        </div>
    </div>
    
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- inpayments-confirmpay -->
