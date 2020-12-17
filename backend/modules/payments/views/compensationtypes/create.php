<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Compensationtypes */

$this->title = Yii::t('app', 'Create Compensationtypes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Compensationtypes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compensationtypes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
