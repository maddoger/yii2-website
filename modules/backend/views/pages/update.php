<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var maddoger\website\models\Page $model
 */

$this->title = \Yii::t('maddoger/website', 'Update Page: {name}', ['name'=> $model->title]);
$this->params['breadcrumbs'][] = ['label' => \Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('maddoger/website', 'Update');
?>
<div class="page-update">
	<?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
