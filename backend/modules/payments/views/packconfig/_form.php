<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Packconfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="packconfig-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'packId')->textInput() ?>

    <?= $form->field($model, 'rankId')->textInput() ?>

    <?= $form->field($model, 'relevel')->textInput() ?>

    <?= $form->field($model, 'cmpsntype')->textInput() ?>

    <?= $form->field($model, 'recipientType')->textInput() ?>

    <?= $form->field($model, 'units')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'itemcntrl')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'recordBy')->textInput() ?>

    <?= $form->field($model, 'recordDate')->textInput() ?>

    <?= $form->field($model, 'changedBy')->textInput() ?>

    <?= $form->field($model, 'changeDate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
