<?php

use maddoger\website\common\models\Page;
use maddoger\website\frontend\Module as FrontendModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model maddoger\website\common\models\Page */

$this->title = Yii::t('maddoger/website', 'Preview page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('maddoger/website', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$availableLanguages = FrontendModule::getAvailableLanguages();
$availableLanguagesCombine = array_combine($availableLanguages, $availableLanguages);

$activeLanguage = $model->default_language ?: $availableLanguages[0];

?>
<div class="page-view">


    <div class="panel panel-default">
        <div class="panel-body clearfix">
            <div class="pull-right">
                <?= $model->getAttributeLabel('updated_at'), ': ', Yii::$app->formatter->asDatetime($model->updated_at) ?>
            </div>

            <?= Html::a(Yii::t('maddoger/website', 'Update'), ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary']) ?> &nbsp;

            <div class="btn-group">
                <button type="button"
                        class="btn btn-<?= $model->status == Page::STATUS_ACTIVE ? 'success' : 'warning' ?> dropdown-toggle"
                        data-toggle="dropdown">
                    <?= Yii::t('maddoger/website', 'Status') . ': ' . $model->getStatusDescription() ?> <span
                        class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($model->getStatusList() as $key => $desc) {
                        echo Html::tag('li', Html::a($desc, ['status', 'id' => $model->id, 'status' => $key]));
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
                echo Html::tag('li',
                    Html::a($language, '#i18n_' . $language, ['data-toggle' => 'tab']),
                    ['class' => $language == $activeLanguage ? 'active' : '']
                );
            }
            ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($availableLanguages as $language) :
                $model->setLanguage($language);
                ?>
                <div class="tab-pane <?= $language == $activeLanguage ? 'active' : '' ?>"
                     id="i18n_<?= $language ?>">
                    <div class="text-content">
                        <h1><?= $model->title ?></h1>
                        <?= $model->getFormattedText() ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
