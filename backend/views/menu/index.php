<?php

/* @var yii\web\View $this */
/* @var maddoger\website\common\models\Menu[] $menus */
/* @var maddoger\website\common\models\Menu[] $items */
/* @var maddoger\website\common\models\Menu $menu */
/* @var maddoger\website\common\models\Menu $newItem */
use maddoger\core\i18n\I18N;
use maddoger\website\backend\BackendAsset;
use maddoger\website\common\models\Menu;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

BackendAsset::register($this);

$this->title = Yii::t('maddoger/website', 'Menus');
$this->params['breadcrumbs'][] = $this->title;

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
            panel.find('.overlay, .loading-img').hide();
        });
        return false;
    });
JS
);

?>
<div class="menu-editor">

<div class="panel panel-default">
    <div class="panel-body">
        <?php
        if (count($menus) > 1) {
            Html::beginForm([''], 'get');
            echo Yii::t('maddoger/website', 'Choose menu for editing'), ': ',
            Html::dropDownList('id', $menu->id, ArrayHelper::map($menus, 'id', 'title'),
                ['id' => 'menu-id-select']);
            echo Html::submitButton(Yii::t('maddoger/website', 'Choose'), ['class' => 'btn btn-default']);
            echo Html::endForm();
        } else {
            echo Yii::t('maddoger/website', 'Update menu bellow or <a href="{url}">create a new menu</a>.',
                ['url' => Url::to(['', 'id' => 0])]);
        }
        ?>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                <div class="panel-title"><?= Yii::t('maddoger/website', 'Links') ?></div>
            </div>
            <div class="panel-body">
                <?php $customLinkForm = ActiveForm::begin([
                    'action' => '#',
                    'id' => 'link-form',
                ]); ?>
                <?= Html::activeHiddenInput($newItem, 'type', ['value' => Menu::TYPE_LINK]); ?>
                <?= $customLinkForm->field($newItem, 'link')->textInput(['value' => empty($newItem->link) ? 'http://' : $newItem->link]) ?>
                <?= $customLinkForm->field($newItem, 'title')->textInput() ?>
                <?= Html::submitButton(Yii::t('maddoger/website', 'Add to menu'), ['class' => 'btn btn-default ajax-add']) ?>
                <?php ActiveForm::end() ?>
            </div>
            <div class="overlay" style="display: none;"></div>
            <div class="loading-img" style="display: none;"></div>
        </div>

        <div class="panel panel-success">
            <div class="panel-heading">
                <div class="panel-title"><?= Yii::t('maddoger/website', 'Page') ?></div>
            </div>
            <div class="panel-body">
                <?php $pageForm = ActiveForm::begin([
                    'action' => '#',
                    'id' => 'page-form',
                ]); ?>
                <?= Html::activeHiddenInput($newItem, 'type', ['value' => Menu::TYPE_PAGE]); ?>
                <?= $pageForm->field($newItem, 'page_id')->textInput() ?>
                <?= Html::submitButton(Yii::t('maddoger/website', 'Add to menu'), ['class' => 'btn btn-default ajax-add']) ?>
                <?php ActiveForm::end() ?>
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
                <?= Yii::t('maddoger/website', 'Menu name: ') ?> <?= Html::activeTextInput($menu, 'title',
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
                        if ($menu->getChildren()->andWhere(['status' => Menu::STATUS_DRAFT])->count()>0) {
                            echo ' ',Html::tag('small', Yii::t('maddoger/website', 'unsaved changes'), ['class' => 'text-danger']);
                        } ?></h4>
                    <?= Yii::t('maddoger/website', 'Add menu items from the column on the left.') ?>
                    <br />
                    <?= $this->render('_items', ['menu' => $menu, 'items' => $items]); ?>
                    <br />
                    <hr/>
                    <h4><?= Yii::t('maddoger/website', 'Menu settings') ?></h4>
                    <?= $form->field($menu, 'slug', ['options' => ['class' => 'form-group form-group-sm']])->textInput()->hint(Yii::t('maddoger/website', 'You insert the menu to a template using it.')) ?>
                    <?= $form->field($menu, 'language', ['options' => ['class' => 'form-group form-group-sm']])->dropDownList(array_merge(['' => '-'], I18N::getAvailableLanguagesList()))->hint(Yii::t('maddoger/website', 'The menu is for one language.')) ?>
                    <?= $form->field($menu, 'css_class', ['options' => ['class' => 'form-group form-group-sm']])->textInput()->hint(Yii::t('maddoger/website', 'The menu UL element will receive this class.')) ?>
                    <?= $form->field($menu, 'element_id', ['options' => ['class' => 'form-group form-group-sm']])->textInput()->hint(Yii::t('maddoger/website', 'The menu UL element will receive this ID.')) ?>
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