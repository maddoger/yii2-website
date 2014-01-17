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
			'title',
			'window_title',
			[
				'name' => 'slug',
				'format' => 'html',
				'value' => '<a target="_blank" href="'.Html::encode(Yii::getAlias('@frontendUrl'.$model->slug)).'">'.Html::encode($model->slug).'</a>',
			],
			[
				'label' => Yii::t('rusporting/website', 'Published'),
				'format' => 'text',
				'value' => $model->getPublishedValue()
			],
			'meta_keywords',
			'meta_description',
			'locale',
			'layout',
			'create_time:datetime',
			'create_user_id',
			'update_time:datetime',
			'update_user_id',
		],
	]); ?>

	<?php
		echo $model->text;
	?>

</div>
