<?php

use maddoger\core\i18n\I18N;
use maddoger\textformats\widgets\FormatDropdown;
use maddoger\textformats\widgets\TextEditor;
use maddoger\website\backend\Module as BackendModule;
use maddoger\website\common\models\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var maddoger\website\common\models\Page $model */
/* @var  maddoger\website\common\models\Menu[] $menus */
/* @var $form yii\widgets\ActiveForm */


$availableLanguages = I18N::getAvailableLanguages();

$activeLanguage = $model->default_language;
if (!$activeLanguage) {
    $ls = $model->getTranslatedLanguages();
    if ($ls) {
        $activeLanguage = $ls[0];
    }
}
if (!$activeLanguage) {
    $activeLanguage = $availableLanguages[0]['locale'];
}

$layouts = BackendModule::getInstance()->config->layouts;
$layouts = $layouts ? array_merge(['' => Yii::t('maddoger/website', 'Default')],
    $layouts) : ['' => Yii::t('maddoger/website', 'Default')];

$textFormats = ArrayHelper::getColumn(BackendModule::getInstance()->textFormats, 'label', true);

$leavePageMessage = Yii::t('maddoger/website', 'Changes are not saved. Are you sure want to leave this page?');
$deleteTranslationMessage = Yii::t('maddoger/website', 'Are you sure want to delete this translation?');
$resetFieldMessage = Yii::t('maddoger/website', 'Are you sure want to reset the field?');
$deleteFieldMessage = Yii::t('maddoger/website', 'Are you sure want to delete the field?');
$deleteFieldLabel = Yii::t('maddoger/website', 'Delete field');
$backupUrl = Url::to(['backup', 'id' => $model->id]);
$changeFormatMessage = Yii::t('maddoger/website', 'Are you sure want to change text format and load another editor?');
$changeFormatUrl = Url::to(['change-format']);

$this->registerJs(
    <<<JS

    $('#delete-translation').click(function(){
        if (confirm('{$deleteTranslationMessage}')) {
            $('#translations .tab-pane.active').find('input, textarea').val('');
        }
        return false;
    });

    $('.meta-data-add-new').click(function(){
        var t = $(this);
        var row = t.prev();
        var name = row.find('input[type="text"]');
        var value = row.find('textarea');
        var error = false;
        if (name.val() == '') {
            error = true;
            name.parent().addClass('has-error');
        } else {
            name.parent().removeClass('has-error');
        }
        if (value.val() == '') {
            error = true;
            value.parent().addClass('has-error');
        } else {
            value.parent().removeClass('has-error');
        }
        if (!error) {
            //Add new add row
            var newRow = row.clone(true, true);
            newRow.find('input, textarea').val('');

            newRow.insertAfter(row);

            row.children().first().append('<br /><button type="button" class="btn btn-danger btn-xs meta-data-delete">{$deleteFieldLabel}</button>');
            row.after('<hr />');

        }
        return false;
    });
    $('.page-form').on('click', '.meta-data-reset', function(){
        if (confirm('{$resetFieldMessage}')) {
            $(this).closest('.row').find('input, textarea').each(function(){
                $(this).val($(this).data('value'));
            });
        }
        return false;
    }).on('click', '.meta-data-delete', function(){
        if (confirm('{$deleteFieldMessage}')) {
            var r = $(this).closest('.row');
            r.next('hr').remove();
            r.remove();
        }
        return false;
    });

    //Backup

    var onBeforeUploadBind = false;
    var backupInterval;
    $('#page-form').on('change', '*', function(){
        if (!onBeforeUploadBind) {
            onBeforeUploadBind = true;
            window.onbeforeunload = function(e) {
              return '{$leavePageMessage}';
            };
        }
        performBackup();
        if (backupInterval === undefined) {
            backupInterval = setInterval(performBackup, 60000);
        }
    }).submit(function(){
        window.onbeforeunload = null;
        onBeforeUploadBind = false;
    });

    function performBackup()
    {
        var data = $('#page-form').serialize();
        $.post("{$backupUrl}", data, function(responce){
        });
    }
    $(window).keydown(function(event) {
	    if (!(event.which == 83 && (event.ctrlKey || event.metaKey))) return true;
        performBackup();
        event.preventDefault();
        return false;
    });
JS
);

?>

<div class="page-form">

<?php $form = ActiveForm::begin(['id' => 'page-form']); ?>
<div class="row">
    <div class="col-md-8">
        <div class="nav-tabs-custom" id="translations">
            <ul class="nav nav-tabs">
                <li class="pull-right tools">
                    <button id="delete-translation" class="btn btn-danger btn-xs"><i
                            class="glyphicon glyphicon-trash"></i> <?= Yii::t('maddoger/website',
                            'Delete this translation') ?></button>
                </li>
                <li class="header"><?= Yii::t('maddoger/website', 'Content') ?></li>
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
                    if (!$modelI18n->text_format) {
                        $modelI18n->text_format = BackendModule::getInstance()->config->defaultTextFormat;
                    }
                    ?>
                    <div class="tab-pane <?= $language['locale'] == $activeLanguage ? 'active' : '' ?>"
                         id="i18n_<?= $language['locale'] ?>">
                        <?= $form->field($modelI18n, 'title', ['enableClientValidation' => false])
                            ->textInput(['maxlength' => 150]) ?>

                        <?= $form->field($modelI18n, 'text_format', ['enableClientValidation' => false])
                                ->widget(FormatDropdown::className(), [
                                    'changeFormatMessage' => $changeFormatMessage,
                                    'changeFormatUrl' => $changeFormatUrl,
                        ]) ?>

                        <?= $form->field($modelI18n, 'text_source', [
                                'enableClientValidation' => false
                            ])->widget(TextEditor::className());
                        ?>

                        <?= $form->field($modelI18n, 'window_title', ['enableClientValidation' => false])
                            ->textInput(['maxlength' => 150])
                            ->hint(Yii::t('maddoger/website', 'Page title for Search Engines.')) ?>

                        <?= $form->field($modelI18n, 'meta_keywords', ['enableClientValidation' => false])
                            ->textarea(['rows' => 4])
                            ->hint(Yii::t('maddoger/website',
                                'Keywords of the page separated by commas. Example: <code>bread, cookies</code>.')) ?>
                        <?= $form->field($modelI18n, 'meta_description', ['enableClientValidation' => false])
                            ->textarea(['rows' => 4])
                            ->hint(Yii::t('maddoger/website', 'Short description of the page.')) ?>

                        <?php
                        $customFieldsInput = '';
                        if (is_array($modelI18n->meta_data)) {
                            foreach ($modelI18n->meta_data as $name => $value) {
                                $customFieldsInput .=
                                    '<div class="row"><div class="col-sm-6">' .
                                    Html::textInput(
                                        Html::getInputName($modelI18n, 'meta_data') . '[name][]',
                                        $name,
                                        [
                                            'class' => 'form-control',
                                            'data-value' => $name,
                                        ]
                                    )
                                    . '<br />
                                            <button type="button" class="btn btn-warning btn-xs meta-data-reset">' . Yii::t('maddoger/website',
                                        'Reset field') . '</button> &nbsp;  <button type="button" class="btn btn-danger btn-xs meta-data-delete">' . Yii::t('maddoger/website',
                                        'Delete field') . '</button>' .
                                    '</div><div class="col-sm-6">' .
                                    Html::textarea(
                                        Html::getInputName($modelI18n, 'meta_data') . '[value][]',
                                        $value,
                                        [
                                            'class' => 'form-control',
                                            'data-value' => $value,
                                        ]
                                    )
                                    . '</div></div><hr />';
                            }
                        }
                        $customFieldsInput .=
                            '<div class="row meta-data-new"><div class="col-sm-6">' .
                            Html::textInput(
                                Html::getInputName($modelI18n, 'meta_data') . '[name][]',
                                '',
                                [
                                    'placeholder' => Yii::t('maddoger/website', 'Name of field'),
                                    'class' => 'form-control',
                                ]
                            )
                            . '</div><div class="col-sm-6">' .
                            Html::textarea(
                                Html::getInputName($modelI18n, 'meta_data') . '[value][]',
                                '',
                                [
                                    'placeholder' => Yii::t('maddoger/website', 'Value'),
                                    'class' => 'form-control',
                                ]
                            )
                            . '</div></div>' . Html::button(
                                Yii::t('maddoger/website', 'Add custom field'),
                                [
                                    'type' => 'button',
                                    'class' => 'btn btn-default btn-xs meta-data-add-new',
                                ]
                            );

                        echo $form->field($modelI18n, 'meta_data',
                            ['parts' => ['{input}' => $customFieldsInput]])->
                        hint(Yii::t('maddoger/website',
                                'Custom fields can be used to add extra metadata to a page that you can use in your theme.'));
                        ?>
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
                <?= $form->field($model, 'slug')->textInput([
                    'maxlength' => 150,
                    'placeholder' => Yii::t('maddoger/website', 'Generate using title')
                ])
                    ->hint(Yii::t('maddoger/website',
                        'URL where page will be published. Example: <code>index</code> will be <code>{domain}/{language}/index</code>.',
                        [
                            'domain' => Yii::$app->request->hostInfo . Yii::getAlias('@frontendUrl'),
                            'language' => substr($activeLanguage, 0, 2),
                        ]))
                ?>

                <?= $form->field($model, 'status')->dropDownList($model::getStatusList())
                    ->hint(Yii::t('maddoger/website', 'Who will see this page?')) ?>

                <?= $form->field($model, 'default_language')->dropDownList(
                    array_merge(['' => Yii::t('maddoger/website', 'Not use')],
                        ArrayHelper::map($availableLanguages, 'locale', 'name'))
                )->hint(Yii::t('maddoger/website',
                    'If needed language version not found, which version should use?')) ?>

                <?= $form->field($model, 'layout')->dropDownList($layouts)
                    ->hint(Yii::t('maddoger/website', 'As this page will look like.')) ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><?= Yii::t('maddoger/website', 'Menu') ?></div>
            </div>
            <div class="panel-body">
                <?php
                if (!$menus[0]->isNewRecord) {
                    $list = [];
                    foreach ($menus as $menu) {

                        $checked = $model->getTranslation($menu->language)->oldAttributes['title'] == $menu->label;

                        $list[] = Html::checkbox(
                            'menu-items-update[' . $menu->id . ']',
                            $checked,
                            ['label' => $menu->label]
                        );
                    }
                    echo Html::tag('p', Yii::t('maddoger/website', 'Update label for this menu items:'));
                    echo Html::ul($list, ['encode' => false, 'class' => 'list-unstyled']);
                } else {
                    echo Html::checkbox('menu-items-create', false, [
                        'label' => Yii::t('maddoger/website', 'Create menu item for page'),
                    ]); ?>
                    <div class="form-group">
                        <label class="control-label"
                               for="menu-items-create-parent_id"><?= Yii::t('maddoger/website',
                                'Menu item parent') ?></label>
                        <?= Html::dropDownList('menu-items-create-parent_id', null, Menu::getList(),
                            ['id' => 'menu-items-create-parent_id', 'class' => 'form-control']); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>
</div>

<div class="form-group">
    <div class="btn-group">
        <?= Html::submitButton(Yii::t('maddoger/website', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::submitButton(Yii::t('maddoger/website', 'Save and exit'),
            ['name' => 'redirect', 'value' => 'exit', 'class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('maddoger/website', 'Save and create new'),
            ['name' => 'redirect', 'value' => 'new', 'class' => 'btn btn-default']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>
