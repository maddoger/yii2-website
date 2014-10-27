<?php

/**
 * @var yii\web\View $this
 * @var maddoger\website\models\Page $model
 */

$this->title = \Yii::t('maddoger/website', 'Create Page');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
