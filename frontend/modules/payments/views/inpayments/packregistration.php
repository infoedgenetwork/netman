<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\payments\models\Inpayments */

$this->title = Yii::t('app', 'Confirm Payment');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inpayments'), 'url' => ['default']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inpayments-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <h3>MemberId: <?= $model->member ?></h3>
    <?= $this->render('_form_1', [
        'model' => $model,
    ]) ?>

</div>
