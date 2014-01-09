<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var rusporting\website\models\Page $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('rusporting/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-view">

	<p>
		<?= Html::a(\Yii::t('rusporting/website', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?php echo Html::a(\Yii::t('rusporting/website', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data-confirm' => \Yii::t('rusporting/website', 'Are you sure to delete this item?'),
			'data-method' => 'post',
		]); ?>
	</p>

	<?php echo DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'slug',
			'locale',
			'published',
			'title',
			'window_title',
			'text:ntext',
			'meta_keywords',
			'meta_description',
			'create_time:datetime',
			'create_user_id',
			'update_time:datetime',
			'update_user_id',
		],
	]); ?>

</div>
