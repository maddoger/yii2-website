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

	<?php
	/**
	 * @var $createdUser null|\rusporting\user\models\User
	 * @var $updatedUser null|\rusporting\user\models\User
	 */
	$createdUser = $model->created_by_user_id > 0 ? \rusporting\user\models\User::find($model->created_by_user_id) : null;
	$updatedUser = $model->updated_by_user_id > 0 ? \rusporting\user\models\User::find($model->updated_by_user_id) : null;

		echo DetailView::widget([
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
				'format' => 'html',
				'value' => '<span class="label '.
					($model->published == 0 ? 'label-danger' : ($model->published == 3 ? 'label-success' : 'label-warning')).'">'.
					$model->getPublishedValue().'</span>'
			],
			'meta_keywords',
			'meta_description',
			'locale',
			'layout',
			'created_at:datetime',
			[
				'name' => 'created_by_user_id',
				'format' => 'html',
				'value' => $createdUser ? Html::a($createdUser->username, ['/user/users/view', 'id' => $createdUser->id]) : '-',
			],
			'updated_at:datetime',
			[
				'name' => 'updated_by_user_id',
				'format' => 'html',
				'value' => $updatedUser ? Html::a($updatedUser->username, ['/user/users/view', 'id' => $updatedUser->id]) : '-',
			]
		],
	]); ?>

	<?php
		echo $model->text;
	?>

</div>
