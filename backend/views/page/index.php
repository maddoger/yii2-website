<?php

use maddoger\core\i18n\I18N;
use maddoger\website\common\models\Page;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel maddoger\website\backend\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('maddoger/website', 'Pages');
$this->params['breadcrumbs'][] = $this->title;

$availableLanguages = I18N::getAvailableLanguages();


$this->registerJs(
    <<<JS
        $('.status-btn-group').each(function(){
        var container = $(this);
        var button = container.find('button');
        container.find('a').click(function(){
            var link = $(this);
            $.get(link.attr('href'), function(){
                container.removeClass('open');
                button.attr('class', 'dropdown-toggle btn btn-xs btn-'+link.data('class'));
                button.find('.status-desc').text(link.text());

            });
            return false;
        });

    });
JS
);

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
                        'label' => Yii::t('maddoger/website', 'Versions'),
                        'value' => function ($model, $key, $index, $column) use (&$availableLanguages) {

                            $res = '<table class="table table-condensed"><tbody>';
                            /**
                             * @var \maddoger\website\common\models\Page $model
                             */
                            foreach ($availableLanguages as $language) {
                                $model->setLanguage($language['locale']);
                                if (!$model->hasTranslation($language['locale'])) {
                                    continue;
                                }

                                $res .= '<tr><td>' . Html::a($model->title, $model->getUrl(), ['title' => Yii::t('maddoger/website', 'View on main website')]) . '</td><td class="text-right" width="50">' . $language['name'] . '</td></tr>';
                            }
                            $res .= '</tbody></table>';
                            return $res;
                        },
                        'format' => 'raw',
                    ],
                    //'default_language',
                    [
                        'attribute' => 'status',
                        'value' => function ($model, $key, $index, $column) {
                            $res = '
<div class="btn-group status-btn-group">
            <button type="button"
                    class="btn btn-xs btn-' . ($model->status == Page::STATUS_ACTIVE ? 'success' : 'warning') . ' dropdown-toggle"
                    data-toggle="dropdown"><span class="status-desc">' . $model->getStatusDescription() . '</span> <span
                    class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">';

                            foreach ($model->getStatusList() as $key => $desc) {
                                $res .= Html::tag('li', Html::a($desc, ['status', 'id' => $model->id, 'status' => $key],
                                        ['data-class' => ($key == Page::STATUS_ACTIVE ? 'success' : 'warning')]));
                            }
                            $res .= '</ul></div>';
                            return $res;
                        },
                        'format' => 'raw',
                        'options' => [
                            'width' => '100',
                        ],
                        'filter' => Page::getStatusList(),
                    ],
                    // 'created_at',
                    // 'created_by',
                    [
                        'attribute' => 'updated_at',
                        'format' => 'datetime',
                        'options' => [
                            'width' => '150',
                        ],
                    ],
                    //'updated_at:datetime',
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
