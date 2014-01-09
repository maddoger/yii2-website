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

			'id',
			'slug',
			'locale',
			'published',
			'title',
			// 'window_title',
			// 'text:ntext',
			// 'meta_keywords',
			// 'meta_description',
			// 'create_time:datetime',
			// 'create_user_id',
			// 'update_time:datetime',
			// 'update_user_id',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>

</div>
