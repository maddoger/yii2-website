<?php


/* @var $this yii\web\View */
/* @var $model maddoger\website\common\models\Page */

$this->title = Yii::t('maddoger/website', 'Create page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">

    <?= $this->render('_form', [
        'model' => $model,
        'menus' => $menus,
    ]) ?>

</div>
