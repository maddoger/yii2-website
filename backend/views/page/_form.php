<?php

use maddoger\website\frontend\Module as FrontendModule;
use maddoger\website\backend\Module as BackendModule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model maddoger\website\common\models\Page */
/* @var $form yii\widgets\ActiveForm */


$availableLanguages = FrontendModule::getAvailableLanguages();
$availableLanguagesCombine = array_combine($availableLanguages, $availableLanguages);

$activeLanguage = $model->default_language ?: $availableLanguages[0];
$layouts = BackendModule::getInstance()->layouts;
$layouts = $layouts ? array_merge(['' => Yii::t('maddoger/website', 'Default')], $layouts) : ['' => Yii::t('maddoger/website', 'Default')];

$deleteMessage = Yii::t('maddoger/website', 'Are you sure want to delete this translation?');
$this->registerJs(
<<<JS
$('#delete-translation').click(function(){
    if (confirm('{$deleteMessage}')) {
        $('#translations .tab-pane.active').find('input, textarea').val('');
    }
    return false;
});
JS
);

?>

<div class="page-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-8">

            <div class="nav-tabs-custom" id="translations">
                <ul class="nav nav-tabs">
                    <li class="pull-right tools">
                        <button id="delete-translation" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> <?= Yii::t('maddoger/website', 'Delete this translation') ?></button></li>
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
                        $modelI18n = $model->getTranslation($language);
                        ?>
                        <div class="tab-pane <?= $language == $activeLanguage ? 'active' : '' ?>"
                             id="i18n_<?= $language ?>">
                            <?= $form->field($modelI18n, 'title', ['enableClientValidation' => false])
                                ->textInput(['maxlength' => 150]) ?>
                            <?= $form->field($modelI18n, 'text_format', ['enableClientValidation' => false])
                                ->textInput(['maxlength' => 10]) ?>
                            <?= $form->field($modelI18n, 'text', ['enableClientValidation' => false])
                                ->textarea(['rows' => 20]) ?>

                            <?= $form->field($modelI18n, 'window_title', ['enableClientValidation' => false])
                                ->textInput(['maxlength' => 150])
                                ->hint(Yii::t('maddoger/website', 'Page title for Search Engines.')) ?>

                            <?= $form->field($modelI18n, 'meta_keywords', ['enableClientValidation' => false])
                                ->textarea(['rows' => 4])
                                ->hint(Yii::t('maddoger/website', 'Keywords of the page separated by commas. Example: <code>bread, cookies</code>.')) ?>

                            <?= $form->field($modelI18n, 'meta_description', ['enableClientValidation' => false])
                                ->textarea(['rows' => 4])
                                ->hint(Yii::t('maddoger/website', 'Short description of the page.')) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <div class="col-md-4">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><?= Yii::t('maddoger/website', 'Common info') ?></div>
                </div>
                <div class="panel-body">
                    <?= $form->field($model, 'slug')->textInput(['maxlength' => 150])
                        ->hint(Yii::t('maddoger/website', 'URL where page will be published. Example: <code>index</code> will be <code>{domain}/{language}/index</code>.',
                            [
                                'domain' => Yii::$app->request->hostInfo.Yii::getAlias('@frontendUrl'),
                                'language' => substr($activeLanguage, 0, 2),
                            ]))
                    ?>

                    <?= $form->field($model, 'status')->dropDownList($model::getStatusList())
                        ->hint(Yii::t('maddoger/website', 'Who will see this page?')) ?>

                    <?= $form->field($model, 'default_language')->dropDownList(
                        array_merge(['' => Yii::t('maddoger/website', 'Not use')], $availableLanguagesCombine)
                    )->hint(Yii::t('maddoger/website', 'If needed language version not found, which version should use?')) ?>

                    <?= $form->field($model, 'layout')->dropDownList($layouts)
                        ->hint(Yii::t('maddoger/website', 'As this page will look like.')) ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><?= Yii::t('maddoger/website', 'Menu') ?></div>
                </div>
                <div class="panel-body">

                </div>
            </div>

        </div>
    </div>

    <div class="form-group">
        <div class="btn-group">
            <?= Html::submitButton(Yii::t('maddoger/website', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::submitButton(Yii::t('maddoger/website', 'Save and exit'), ['name' => 'redirect', 'value' => 'exit', 'class' => 'btn btn-default']) ?>
            <?= Html::submitButton(Yii::t('maddoger/website', 'Save and create new'), ['name' => 'redirect', 'value' => 'new', 'class' => 'btn btn-default']) ?>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
