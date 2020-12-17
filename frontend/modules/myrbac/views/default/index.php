<?php
use yii\helpers\Html;


$this->title = Yii::t('app', 'RBAC Switchboard');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Admin Switchboard'), 'url' => ['/switchboard/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="myrbac-default-index">
    <h1><?= $this->title ?></h1>
    
    <div class="col-sm-4">
        <!--<?= Html::a(Yii::t('app','Add Permission'),['authconsole/permissionorrole'],['class'=>'btn btn-success btn-block']) ?></br>-->
        <?= Html::a(Yii::t('app','Add Permission'),['authitem/create-permission'],['class'=>'btn btn-success btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Add Role'),['authitem/create-role'],['class'=>'btn btn-success btn-block']) ?></br>
        <!--<?= Html::a(Yii::t('app','Add Role'),['authitem/create-role'],['class'=>'btn btn-success btn-block']) ?></br>-->
        <?= Html::a(Yii::t('app','Roles/Permissions Hierarchy'),['authitemchild/create'],['class'=>'btn btn-success btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Assign Roles/Permissions'),['authassignment/create'],['class'=>'btn btn-success btn-block']) ?></br>
        <?= Html::a(Yii::t('app','Add Authorization Rule'),['authrule/create'],['class'=>'btn btn-success btn-block']) ?></br>
    </div>
    <div class="col-sm-4">
        
    </div>
</div>
