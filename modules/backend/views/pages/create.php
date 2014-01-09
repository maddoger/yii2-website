<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var rusporting\website\models\Page $model
 */

$this->title = \Yii::t('rusporting/website', 'Create Page');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('rusporting/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-create">
	<?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
