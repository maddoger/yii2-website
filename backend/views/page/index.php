<?php

use maddoger\website\frontend\Module;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel maddoger\website\backend\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('maddoger/website', 'Pages');
$this->params['breadcrumbs'][] = $this->title;

$availableLanguages = Module::getAvailableLanguages();

?>
<div class="page-index">

    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('maddoger/website', 'Create page'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    //'id',
                    'slug',
                    'title',
                    [
                        'attribute' => 'availableTranslations',
                        'value' => function ($model, $key, $index, $column){
                            return Html::ul($model->availableTranslations, ['class' => 'list-unstyled']);
                        },
                        'format' => 'html',
                        'filter' => $availableLanguages,
                    ],

                    'default_language',
                    'status',
                    // 'created_at',
                    // 'created_by',
                    // 'updated_at',
                    // 'updated_by',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
