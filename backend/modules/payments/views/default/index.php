<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Payment Config');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Admin Switchboard'), 'url' => ['/switchboard/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-default-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-lg-4">
        <?= Html::a(Yii::t('app','Confirm Payments'),['inpayments/index'],['class'=>'btn btn-primary btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Manage Ranks'),['ranks/create'],['class'=>'btn btn-primary btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Manage Pay Types'),['paymenttypes/create'],['class'=>'btn btn-primary btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Manage Payment Methods'),['paymethods/create'],['class'=>'btn btn-primary btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Manage Packages'),['packages/create'],['class'=>'btn btn-primary btn-block']) ?></br>
    </div>

</div>
