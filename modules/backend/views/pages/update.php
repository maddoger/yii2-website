<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var rusporting\website\models\Page $model
 */

$this->title = \Yii::t('rusporting/website', 'Update Page: {name}', ['name'=> $model->title]);
$this->params['breadcrumbs'][] = ['label' => \Yii::t('rusporting/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('rusporting/website', 'Update');
?>
<div class="page-update">
	<?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
