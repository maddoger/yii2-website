<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $model maddoger\website\common\models\Page */

$this->title = Yii::t('maddoger/website', 'Update page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('maddoger/website', 'Update');

$this->params['header'] = $this->title.'&nbsp'.Html::a(Yii::t('maddoger/website', 'Create new'), ['create'], ['class' => 'btn btn-default btn-sm']);
?>
<div class="page-update">

    <?= $this->render('_form', [
        'model' => $model,
        'menus' => $menus,
    ]) ?>

</div>
