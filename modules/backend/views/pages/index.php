<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var rusporting\website\modules\backend\models\PageSearch $searchModel
 */

$this->title = \Yii::t('rusporting/website', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a(\Yii::t('rusporting/website', 'Create Page'), ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			[
				'value' => function ($model, $index, $widget){
						return '<a target="_blank" href="'.Html::encode(Yii::getAlias('@frontendUrl'.$model->slug)).'">'.Html::encode($model->slug).'</a>';
					},
				'format' => 'html',
				'attribute' => 'slug',
			],
			'title',
			[
				'value' => function ($model, $index, $widget){
						return $model->getPublishedValue();
					},
				'filter' => \rusporting\website\models\Page::publishListValues(),
				'attribute' => 'published',
			],
			//'locale',
			// 'window_title',
			// 'text:ntext',
			// 'meta_keywords',
			// 'meta_description',
			// 'created_at:datetime',
			// 'created_by_user_id',
			'updated_at:datetime',
			// 'updated_by_user_id',

			['class' => 'yii\grid\ActionColumn',
				'template' => '<span class="grid-actions">{view} {update} {delete} &nbsp; &nbsp; {copy}</span>',
				'buttons' => [
					'view' => function ($url, $model) {
							return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
								'class' => 'btn btn-info grid-action-view',
								'title' => Yii::t('rusporting/website', 'View'),
							]);
						},
					'update' => function ($url, $model) {
							return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
								'class' => 'btn btn-success grid-action-update',
								'title' => Yii::t('rusporting/website', 'Update'),
							]);
						},
					'delete' => function ($url, $model) {
							return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
								'class' => 'btn btn-danger grid-action-delete',
								'title' => Yii::t('rusporting/website', 'Delete'),
								'data-confirm' => Yii::t('rusporting/website', 'Are you sure to delete this item?'),
								'data-method' => 'post',
							]);
						}
					,
					'copy' => function ($url, $model) {
							return \yii\helpers\Html::a('<span class="fa fa-copy"></span>', $url, [
								'class' => 'btn btn-success grid-action-copy',
								'title' => Yii::t('rusporting/website', 'Copy'),
							]);
						}

				]
			],
		],
	]); ?>

</div>
