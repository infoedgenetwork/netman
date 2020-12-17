<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title ='Admin Switchboard'
?>
<h1>Admin Switchboard</h1>
<div class="col-lg-4">
    <?= Html::a(Yii::t('app','Manage Membership'),['/geneology/sponsorship/create'],['class'=>'btn btn-primary btn-block']) ?></br>
    <?= Html::a(Yii::t('app','Manage Statuses'),['/geneology/statuses/create'],['class'=>'btn btn-primary btn-block']) ?></br>
    <?= Html::a(Yii::t('app','Manage Authorization'),['/myrbac/default/index'],['class'=>'btn btn-primary btn-block']) ?></br>
    <?= Html::a(Yii::t('app','Payment Configuration'),['/payments/default/index'],['class'=>'btn btn-primary btn-block']) ?></br>
</div>
<div class="col-lg-4">
    
</div>
<div class="col-lg-4">
    
</div>
