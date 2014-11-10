<?php

/* @var yii\web\View $this */
use maddoger\website\common\models\Menu;
use yii\helpers\Html;

/* @var array|maddoger\website\common\models\Menu $item */

switch ($item['type']) {
    case Menu::TYPE_LINK:
        $itemTypeLabel = Yii::t('maddoger/website', 'Link');
        break;
    case Menu::TYPE_PAGE:
        $itemTypeLabel = Yii::t('maddoger/website', 'Page');
        break;

    default:
        $itemTypeLabel = Yii::t('maddoger/website', 'Custom');
}

$item->scenario = 'updateMenuItems';

?>
<li id="menu-items-<?= $item['id'] ?>" data-id="<?= $item['id'] ?>">
    <div class="panel panel-solid panel-default collapsed-panel">
        <div class="panel-heading">
            <div class="panel-tools pull-right">
                <?= $itemTypeLabel ?>
                <button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
            </div>
            <div class="panel-title"><?= $item['title'] ?></div>
        </div>
        <div class="panel-body" style="display: none;">
            <?php
            //$fieldPrefix = 'menu-items['.$item['id'].']';
            //$idPrefix = 'menu-items-'.$item['id'];
            echo Html::hiddenInput('items_sort[]', $item['id']);
            echo Html::activeHiddenInput($item, 'parent_id');
            ?>
            <div class="form-group form-group-sm">
                <?= Html::activeLabel($item, 'link', ['class' => 'control-label']) ?>
                <?= Html::activeTextInput($item, 'link', ['class' => 'form-control']) ?>
            </div>
            <div class="form-group form-group-sm">
                <?= Html::activeLabel($item, 'title', ['class' => 'control-label']) ?>
                <?= Html::activeTextInput($item, 'title', ['class' => 'form-control']) ?>
            </div>
            <hr />
            <?php if ($item->type == Menu::TYPE_PAGE && $item->page) { ?>
            <div class="form-group form-group-sm">
                <label class="control-label"><?=  Yii::t('maddoger/website', 'Original page') ?>:</label>
                <?= Html::a(Html::encode($item->page->title), ['page/view', 'id' => $item->page_id]) ?>
            </div>
            <hr />
            <?php } ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group form-group-sm">
                        <?= Html::activeLabel($item, 'icon_class', ['class' => 'control-label']) ?>
                        <?= Html::activeTextInput($item, 'icon_class', ['class' => 'form-control']) ?>
                        <div class="hint-block"><?= Yii::t('maddoger/website', 'For example: <code>fa fa-home</code> is <i class="fa fa-home"></i>') ?></div>
                    </div>
                    <div class="form-group form-group-sm">
                        <?= Html::activeLabel($item, 'target', ['class' => 'control-label']) ?>
                        <?= Html::activeDropDownList($item, 'target',
                            [
                                '' => Yii::t('maddoger/website', 'Current window/tab'),
                                '_blank' => Yii::t('maddoger/website', 'New window/tab'),
                                '_top' => Yii::t('maddoger/website', 'Top window/tab'),
                            ],
                            ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group form-group-sm">
                        <?= Html::activeLabel($item, 'css_class', ['class' => 'control-label']) ?>
                        <?= Html::activeTextInput($item, 'css_class', ['class' => 'form-control']) ?>
                        <div class="hint-block"><?= Yii::t('maddoger/website', 'Class name for li element.') ?></div>
                    </div>
                    <div class="form-group form-group-sm">
                        <?= Html::activeLabel($item, 'element_id', ['class' => 'control-label']) ?>
                        <?= Html::activeTextInput($item, 'element_id', ['class' => 'form-control']) ?>
                    </div>
                </div>
            </div>
            <hr />
            <div class="form-group form-group-sm">
                <?= Html::activeLabel($item, 'preg', ['class' => 'control-label']) ?>
                <?= Html::activeTextInput($item, 'preg', ['class' => 'form-control']) ?>
                <div class="hint-block"><?= Yii::t('maddoger/website', 'Custom activity regular expression.') ?></div>
            </div>
        </div>
    </div>
    <ol>
        <?php
        if (isset($item['children']) && !empty($item['children'])) {
            foreach ($item['children'] as $child) {
                echo $this->render('_item', ['item' => $child]);
            }
        }
        ?>
    </ol>
</li>