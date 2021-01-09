<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Tblpoints */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tblpoints-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'sponsor')->textInput() ?>

    <?= $form->field($model, 'memberFrom')->textInput() ?>

    <?= $form->field($model, 'trxType')->textInput() ?>

    <?= $form->field($model, 'dateEarned')->textInput() ?>

    <?= $form->field($model, 'trxDate')->textInput() ?>

    <?= $form->field($model, 'trxBy')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
