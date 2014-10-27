<?php

use maddoger\website\common\models\Page;
use maddoger\website\frontend\Module;
use yii\grid\GridView;
use yii\helpers\Html;

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
                <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('maddoger/website', 'Create page'),
                    ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-hover table-striped'
                ],
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    //'id',
                    [
                        'attribute' => 'slug',
                        'options' => [
                            'width' => '200',
                        ],
                    ],
                    /*[
                         'attribute' => 'availableTranslations',
                         'value' => function ($model, $key, $index, $column){
                             return Html::ul($model->availableTranslations, ['class' => 'list-unstyled']);
                         },
                         'format' => 'html',
                         'filter' => $availableLanguages,
                     ],*/

                    [
                        'attribute' => 'title',
                        'label' => Yii::t('maddoger/website', 'Translations'),
                        'value' => function ($model, $key, $index, $column) use (&$availableLanguages) {

                            $res = '<table class="table table-condensed"><tbody>';
                            /**
                             * @var \maddoger\website\common\models\Page $model
                             */
                            foreach ($availableLanguages as $language) {
                                $model->setLanguage($language);
                                $res .= '<tr><td>'.$model->title.'</td><td class="text-right" width="50">'.$model->language.'</td></tr>';
                            }
                            $res .='</tbody></table>';
                            return $res;
                        },
                        'format' => 'raw',
                    ],
                    //'default_language',
                    [
                        'attribute' => 'status',
                        'value' => function($model, $key, $index, $column) {
                            return Html::tag('span', $model->getStatusDescription(),
                                ['class' => 'label label-'.($model->status == Page::STATUS_ACTIVE ? 'success' : 'warning')]);
                        },
                        'format' => 'html',
                        'options' => [
                            'width' => '100',
                        ],
                        'filter' => Page::getStatusList(),
                    ],
                    // 'created_at',
                    // 'created_by',
                    // 'updated_at',
                    // 'updated_by',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'options' => [
                            'width' => '100',
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
