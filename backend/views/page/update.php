<?php

/* @var $this yii\web\View */
/* @var $model maddoger\website\common\models\Page */

$this->title = Yii::t('maddoger/website', 'Update page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('maddoger/website', 'Update');
?>
<div class="page-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
