<?php

use maddoger\core\i18n\I18N;
use maddoger\website\common\models\Page;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model maddoger\website\common\models\Page */

$this->title = Yii::t('maddoger/website', 'Preview page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$availableLanguages = I18N::getAvailableLanguages();

$activeLanguage = $model->default_language;
if (!$activeLanguage) {
    $ls = $model->getAvailableLanguages();
    if ($ls) {
        $activeLanguage = $ls[0];
    }
}
if (!$activeLanguage) {
    $activeLanguage = $availableLanguages[0]['locale'];
}

$updatedBy = null;
if ($model->updated_by) {
    $userClass = Yii::$app->user->identityClass;
    $updatedByUser = $userClass::findOne($model->updated_by);
    if ($updatedByUser) {
        $updatedBy = Html::a($updatedByUser->username, ['/admin/user/view', 'id' => $model->updated_by]);
    }
}

$this->registerJs(
<<<JS
    $('.status-btn-group').each(function(){
        var container = $(this);
        var button = container.find('button');
        container.find('a').click(function(){
            var link = $(this);
            $.get(link.attr('href'), function(){
                container.removeClass('open');
                button.attr('class', 'dropdown-toggle btn btn-'+link.data('class'));
                button.find('.status-desc').text(link.text());

            });
            return false;
        });

    });
JS
);

?>
<div class="page-view">


    <div class="panel panel-default">
        <div class="panel-body clearfix">
            <div class="pull-right text-right">
                <?= $model->getAttributeLabel('updated_at'), ': ', Yii::$app->formatter->asDatetime($model->updated_at) ?>
                <br/>
                <?= $model->getAttributeLabel('updated_by'), ': ', $updatedBy ?>
            </div>

            <?= Html::a(Yii::t('maddoger/website', 'Update'), ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary']) ?> &nbsp;

            <div class="btn-group status-btn-group">
                <button type="button"
                        class="btn btn-<?= $model->status == Page::STATUS_ACTIVE ? 'success' : 'warning' ?> dropdown-toggle"
                        data-toggle="dropdown">
                    <?= Yii::t('maddoger/website', 'Status') . ': <span class="status-desc">' . $model->getStatusDescription().'</span>' ?> <span
                        class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($model->getStatusList() as $key => $desc) {
                        echo Html::tag('li', Html::a($desc, ['status', 'id' => $model->id, 'status' => $key], ['data-class' => ($key == Page::STATUS_ACTIVE ? 'success' : 'warning')]));
                    } ?>
                </ul>
            </div>
            &nbsp;

            <?= Html::a(Yii::t('maddoger/website', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('maddoger/website', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>

        </div>
    </div>

    <div class="nav-tabs-custom" id="translations">
         <ul class="nav nav-tabs">
                <li class="header"><?= Yii::t('maddoger/website', 'Content') ?></li>
                <?php
                foreach ($availableLanguages as $language) {
                    if (!$model->hasTranslation($language['locale'])) continue;
                    echo Html::tag('li',
                        Html::a($language['name'], '#i18n_' . $language['locale'], ['data-toggle' => 'tab']),
                        ['class' => $language['locale'] == $activeLanguage ? 'active' : '']
                    );
                }
                ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($availableLanguages as $language) :
                if (!$model->hasTranslation($language['locale'])) continue;
                $model->setLanguage($language['locale']);
                $url = $model->getUrl($language['locale']);
                ?>
                <div class="tab-pane <?= $language['locale'] == $activeLanguage ? 'active' : '' ?>"
                     id="i18n_<?= $language['locale'] ?>">
                    <?= $model->getAttributeLabel('slug'), ': ', Html::a($url, $url) ?><br />
                    <div class="text-content">
                        <h1><?= $model->title ?></h1>
                        <?= $model->text ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
