<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\payments\models\Pointtrxtypes */

$this->title = Yii::t('app', 'Create Pointtrxtypes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pointtrxtypes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pointtrxtypes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
