<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use backend\modules\payments\models\Packages;
use backend\modules\payments\models\Pointtrxtypes;



/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Packconfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="packconfig-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-4">
    <?= $form->field($model, 'packId')->dropDownList(ArrayHelper::map(Packages::find()->all(), 'id', 'packName'),['prompt'=> '--Select Package--']) ?>
            </div>
        <div class="col-sm-4">
    <?= $form->field($model, 'trxType')->dropDownList(ArrayHelper::map(Pointtrxtypes::find()->all(), 'id', 'trxTypeName'),['prompt'=> '--Select Transaction Type--']) ?>
            </div>
        <div class="col-sm-4">
    <?= $form->field($model, 'amount')->textInput() ?>
        </div>
    </div>
        

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
