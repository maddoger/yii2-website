<?php

/* @var yii\web\View $this */
/* @var maddoger\website\common\models\Menu[] $menus */
/* @var maddoger\website\common\models\Menu[] $items */
/* @var maddoger\website\common\models\Menu $menu */
/* @var maddoger\website\common\models\Menu $newItem */
use maddoger\core\i18n\I18N;
use maddoger\website\backend\BackendAsset;
use maddoger\website\common\models\Menu;
use maddoger\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

BackendAsset::register($this);

$this->title = Yii::t('maddoger/website', 'Menus');
$this->params['breadcrumbs'][] = $this->title;

$leavePageMessage = Yii::t('maddoger/website', 'Changes are not saved. Are you sure want to leave this page?');

$this->registerJs(
    <<<JS
    $('.ajax-add').click(function(){
        var form = $(this).closest('form');
        var data = form.serialize();
        var panel = form.closest('.panel');
        panel.find('.overlay, .loading-img').show();
        $.post(form.prop('action'), data, function(html){
            $('#menu-items-editor > ol').append(html);
            $("[data-widget='collapse']").collapse();
            form[0].reset();
            form.find('.select2-container').select2("val", "");
            panel.find('.overlay, .loading-img').hide();
        });
        return false;
    });

    var onBeforeUploadBind = false;
    $('#menu-editor-main-container').on('change', '*', function(){
        if (!onBeforeUploadBind) {
            onBeforeUploadBind = true;
            window.onbeforeunload = function(e) {
              return '{$leavePageMessage}';
            };
        }
    }).find('form').submit(function(){
        window.onbeforeunload = null;
        onBeforeUploadBind = false;
    });
JS
);

?>
<div class="menu-editor">

    <div class="panel panel-default">
        <div class="panel-body">
            <?php
            if (count($menus) > 1) {
                echo Html::beginForm([''], 'get', ['class' => 'form-inline']);
                echo Yii::t('maddoger/website', 'Choose menu for editing'), ': ';
                echo Html::dropDownList('id', $menu->id, ArrayHelper::map($menus, 'id', 'label'),
                    ['id' => 'menu-id-select', 'class' => 'form-control input-sm']), '&nbsp;';
                echo Html::submitButton(Yii::t('maddoger/website', 'Choose'),
                    ['class' => 'btn btn-sm btn-default']), '&nbsp;';
                echo Yii::t('maddoger/website', ' or <a href="{url}">create a new menu</a>.',
                    ['url' => Url::to(['', 'id' => 0])]);
                echo Html::endForm();
            } else {
                echo Yii::t('maddoger/website', 'Update menu bellow');
                echo Yii::t('maddoger/website', ' or <a href="{url}">create a new menu</a>.',
                    ['url' => Url::to(['', 'id' => 0])]);
            }
            ?>
        </div>
    </div>

    <div class="row" id="menu-editor-main-container">
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="panel-title"><?= Yii::t('maddoger/website', 'Links') ?></div>
                </div>
                <div class="panel-body">
                    <?php if ($menu->isNewRecord) {
                        echo Yii::t('maddoger/website', 'Create menu first.');
                    } else {
                        $customLinkForm = ActiveForm::begin([
                            'action' => '#',
                            'id' => 'link-form',
                        ]);
                        echo Html::activeHiddenInput($newItem, 'type', ['value' => Menu::TYPE_LINK]);
                        echo $customLinkForm->field($newItem,
                            'link')->textInput(['value' => empty($newItem->link) ? 'http://' : $newItem->link]);
                        echo $customLinkForm->field($newItem, 'label')->textInput();
                        echo Html::submitButton(Yii::t('maddoger/website', 'Add to menu'),
                            ['class' => 'btn btn-default ajax-add']);
                        ActiveForm::end();
                    }
                    ?>
                </div>
                <div class="overlay" style="display: none;"></div>
                <div class="loading-img" style="display: none;"></div>
            </div>

            <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="panel-title"><?= Yii::t('maddoger/website', 'Pages') ?></div>
                </div>
                <div class="panel-body">
                    <?php if ($menu->isNewRecord) {
                        echo Yii::t('maddoger/website', 'Create menu first.');
                    } else {
                        $pageForm = ActiveForm::begin([
                            'action' => '#',
                            'id' => 'page-form',
                        ]);
                        echo Html::activeHiddenInput($newItem, 'type', ['value' => Menu::TYPE_PAGE]);
                        echo Html::activeHiddenInput($newItem, 'label');
                        echo Html::activeHiddenInput($newItem, 'link')
                        ?>

                        <div class="clearfix">
                            <div class="pull-right">
                                <?= Html::dropDownList('sort', 'updated_at', [
                                    'updated_at' => Yii::t('maddoger/website', 'Last updated'),
                                    'title' => Yii::t('maddoger/website', 'By title'),
                                ], ['id' => 'new-item-page-sort']); ?>
                            </div>
                            <?= $pageForm->field($newItem, 'page_id')->widget(Select2::className(), [
                                'clientOptions' => [
                                    'placeholder' => Yii::t('maddoger/website', 'Search pages...'),
                                    'ajax' => [
                                        'url' => Url::to(['pages']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function (term, page) { return { q: term, sort: $("#new-item-page-sort").val() }; }'),
                                        'results' => new JsExpression('function (data, page) { return { results: data }; }'),
                                    ],
                                    'formatResult' => ($formatResult = new JsExpression('function (state) {
                                if (!state.id) return state.text; // optgroup
                                return state.text + " <small class=\"text-muted\">"+state.url+"</small>";
                            }')),
                                    'formatSelection' => $formatResult,
                                ],
                                'clientEvents' => [
                                    'change' => new JsExpression('function (event) {
                                $("#page-form input[name=\"MenuNewItem[label]\"]").val(event.added.title);
                                $("#page-form input[name=\"MenuNewItem[link]\"]").val(event.added.url);
                            }')
                                ],
                            ]) ?>
                        </div>
                        <?= Html::submitButton(Yii::t('maddoger/website', 'Add to menu'),
                            ['class' => 'btn btn-default ajax-add']) ?>
                        <?php ActiveForm::end();
                    } ?>
                </div>
                <div class="overlay" style="display: none;"></div>
                <div class="loading-img" style="display: none;"></div>
            </div>

        </div>
        <div class="col-md-8">
            <?php $form = ActiveForm::begin([
                //'layout' => 'horizontal',
            ]) ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="pull-right">
                        <?= Html::submitButton($menu->isNewRecord ? Yii::t('maddoger/website',
                                'Create menu') : Yii::t('maddoger/website', 'Save menu'),
                            ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                    <?= Yii::t('maddoger/website', 'Menu name: ') ?> <?= Html::activeTextInput($menu, 'label',
                        ['required' => 'required']) ?>
                </div>
                <div class="panel-body">
                    <?php
                    if ($menu->isNewRecord) {
                        echo Yii::t('maddoger/website', 'Give your menu a name above, then click Create menu.');
                    } else {
                        ?>
                        <h4><?php
                            echo Yii::t('maddoger/website', 'Menu structure');
                            if ($menu->getChildren()->andWhere(['status' => Menu::STATUS_DRAFT])->count() > 0) {
                                echo ' ', Html::tag('small', Yii::t('maddoger/website', 'unsaved changes'),
                                    ['class' => 'text-danger']);
                            } ?></h4>
                        <?= Yii::t('maddoger/website', 'Add menu items from the column on the left.') ?>
                        <br/>
                        <?= $this->render('_items',
                            ['menu' => $menu, 'items' => $menu->getChildren()->with(['children', 'page'])->all()]); ?>
                        <br/>
                        <hr/>
                        <h4><?= Yii::t('maddoger/website', 'Menu settings') ?></h4>
                        <?= $form->field($menu, 'slug',
                            ['options' => ['class' => 'form-group form-group-sm']])->textInput()->hint(Yii::t('maddoger/website',
                            'You insert the menu to a template using it.')) ?>
                        <?= $form->field($menu, 'language',
                            ['options' => ['class' => 'form-group form-group-sm']])->dropDownList(array_merge(['' => '-'],
                            I18N::getAvailableLanguagesList()))->hint(Yii::t('maddoger/website',
                            'The menu is for one language.')) ?>
                        <?= $form->field($menu, 'css_class',
                            ['options' => ['class' => 'form-group form-group-sm']])->textInput()->hint(Yii::t('maddoger/website',
                            'The menu UL element will receive this class.')) ?>
                        <?= $form->field($menu, 'element_id',
                            ['options' => ['class' => 'form-group form-group-sm']])->textInput()->hint(Yii::t('maddoger/website',
                            'The menu UL element will receive this ID.')) ?>
                    <?php } ?>
                </div>
                <div class="panel-footer clearfix">
                    <?php if (!$menu->isNewRecord) {
                        echo Html::a(Yii::t('maddoger/website', 'Delete menu'), ['delete', 'id' => $menu->id], [
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('maddoger/website', 'Are you sure want to delete this menu?'),
                            'class' => 'text-danger'
                        ]);
                    } ?>
                    <div class="pull-right">
                        <?= Html::submitButton($menu->isNewRecord ? Yii::t('maddoger/website',
                                'Create menu') : Yii::t('maddoger/website', 'Save menu'),
                            ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
            <?php $form->end() ?>
        </div>
    </div>