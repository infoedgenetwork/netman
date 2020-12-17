<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Packconfig */

$this->title = Yii::t('app', 'Create Packconfig');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Packconfigs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packconfig-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
