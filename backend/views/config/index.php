<?php

/* @var $this yii\web\View */
use maddoger\core\i18n\I18N;
use maddoger\website\backend\Module as BackendModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model maddoger\website\common\models\Page */

$this->title = Yii::t('maddoger/website', 'Configuration');

$availableLanguages = I18N::getAvailableLanguages();
$activeLanguage = $availableLanguages[0]['locale'];

$layouts = BackendModule::getInstance()->config->layouts;
$layouts = $layouts ? array_merge(['' => Yii::t('maddoger/website', 'Default')],
    $layouts) : ['' => Yii::t('maddoger/website', 'Default')];

$textFormats = BackendModule::getInstance()->textFormats;
$textFormats = array_merge(['' => Yii::t('maddoger/website', 'Default')],
    $textFormats ? ArrayHelper::getColumn($textFormats, 'label', true) : []);


?>
<div class="page-update">

    <div class="page-form">

        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">

                <div class="nav-tabs-custom" id="translations">
                    <ul class="nav nav-tabs">
                        <li class="header"><?= Yii::t('maddoger/website', 'SEO') ?></li>
                        <?php
                        foreach ($availableLanguages as $language) {
                            echo Html::tag('li',
                                Html::a($language['name'], '#i18n_' . $language['locale'], ['data-toggle' => 'tab']),
                                ['class' => $language['locale'] == $activeLanguage ? 'active' : '']
                            );
                        }
                        ?>
                    </ul>
                    <div class="tab-content">
                        <?php foreach ($availableLanguages as $language) :
                            $modelI18n = $model->getTranslation($language['locale']);
                            ?>
                            <div class="tab-pane <?= $language['locale'] == $activeLanguage ? 'active' : '' ?>"
                                 id="i18n_<?= $language['locale'] ?>">
                                <?= $form->field($modelI18n, 'title', ['enableClientValidation' => false])
                                    ->textInput(['maxlength' => 150]) ?>

                                <?= $form->field($modelI18n, 'meta_keywords', ['enableClientValidation' => false])
                                    ->textarea(['rows' => 4])
                                    ->hint(Yii::t('maddoger/website',
                                        'Keywords of the page separated by commas. Example: <code>bread, cookies</code>.')) ?>

                                <?= $form->field($modelI18n, 'meta_description', ['enableClientValidation' => false])
                                    ->textarea(['rows' => 4])
                                    ->hint(Yii::t('maddoger/website', 'Short description of the page.')) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <div class="col-md-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title"><?= Yii::t('maddoger/website', 'Common') ?></div>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'defaultTextFormat')->dropDownList($textFormats)
                            ->hint(Yii::t('maddoger/website', 'Default text format for pages.')) ?>

                        <?= $form->field($model, 'defaultLayout')->dropDownList($layouts)
                            ->hint(Yii::t('maddoger/website', 'As this page will look like.')) ?>

                        <?= $form->field($model, 'beginBodyScripts')->textarea(['rows' => 7])
                            ->hint(Yii::t('maddoger/website', 'Google, LiveInternet, etc.')) ?>

                        <?= $form->field($model, 'endBodyScripts')->textarea(['rows' => 7])
                            ->hint(Yii::t('maddoger/website', 'Google, LiveInternet, etc.')) ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="form-group">
            <div class="btn-group">
                <?= Html::submitButton(Yii::t('maddoger/website', 'Save'),
                    ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
