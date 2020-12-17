<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Ranks */

$this->title = Yii::t('app', 'Update Ranks');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ranks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment Config'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Add Ranks'), 'url' => ['create']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ranks-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
