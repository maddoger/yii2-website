<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\frontend\widgets;

use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu as BaseMenu;

/**
 * Menu
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */
class Menu extends BaseMenu
{
    /**
     * @var \maddoger\website\common\models\Menu for items. You can use Menu object, slug or id.
     */
    public $menu;

    /**
     * @var string
     */
    public $menuModelClass = 'maddoger\website\common\models\Menu';

    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}"{target}{title}><span>{icon}{label}</span></a>';

    /**
     * @inheritdoc
     */
    public $labelTemplate = '<span{title}>{icon}{label}</span>';

    /**
     * @var string
     */
    public $iconTemplate = '<i class="{icon}"></i>&nbsp;';

    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul>\n{items}\n</ul>\n";

    /**
     * @var string
     */
    public $submenuItemClass;

    /**
     * @inheritdoc
     */
    public $activateParents = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->items) {

            if (!$this->menu) {
                throw new InvalidParamException('Menu property must be set.');
            }

            $class = $this->menuModelClass;
            if (is_int($this->menu)) {
                $this->menu = $class::findOne($this->menu);
            } elseif (is_string($this->menu)) {
                $this->menu = $class::findBySlug($this->menu);
            }

            if ($this->menu instanceof $class) {
                //Get items
                $this->items = $this->menu->getItems();

                if (!empty($this->menu->element_id) && !isset($this->options['id'])) {
                    $this->options['id'] = $this->menu->element_id;
                }
                if (!empty($this->menu->css_class)) {
                    Html::addCssClass($this->options, $this->menu->css_class);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    /**
     * Normalizes the [[items]] property to remove invisible items and activate certain items.
     * @param array $items the items to be normalized.
     * @param boolean $active whether there is an active child menu item.
     * @return array the normalized menu items
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items as $i => $item) {

            if (isset($item['roles'])) {
                $item['visible'] = false;
                foreach ($item['roles'] as $role) {
                    if (Yii::$app->user->can($role)) {
                        $item['visible'] = true;
                        break;
                    }
                }
            }
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            if (!isset($item['label'])) {
                $item['label'] = '';
            }
            if (!isset($items[$i]['options'])) {
                $items[$i]['options'] = [];
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i]['label'] = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $hasActiveChild = false;
            if (isset($item['items'])) {
                $items[$i]['items'] = $this->normalizeItems($item['items'], $hasActiveChild);
                if (empty($items[$i]['items']) && $this->hideEmptyItems) {
                    unset($items[$i]['items']);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
                if ($this->submenuItemClass) {
                    Html::addCssClass($items[$i]['options'], $this->submenuItemClass);
                }
            }
            if (!isset($item['active'])) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }


    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $icon = ArrayHelper::getValue($item, 'icon_class');
        if ($icon) {
            $icon = '<i class="'.$icon.'"></i>&nbsp;';
        }

        $template = isset($item['url']) ?
            ArrayHelper::getValue($item, 'template', $this->linkTemplate) :
            ArrayHelper::getValue($item, 'template', $this->labelTemplate);

        return strtr($template, [
            '{url}' => isset($item['url']) ? Url::to($item['url']) : null,
            '{icon}' => $icon,
            '{label}' => $item['label'],
            '{target}' =>
                (isset($item['target']) && !empty($item['target'])) ?
                    ' target="'.Html::encode($item['target']).'"' :
                    '',
            '{title}' =>
                (isset($item['title']) && !empty($item['title'])) ?
                    ' title="'.Html::encode($item['title']).'"' :
                    '',

        ]);
    }


    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        $res = parent::isItemActive($item);
        if (!$res) {

            $preg = null;
            if (isset($item['preg']) && !empty($item['preg'])) {
                $preg = $item['preg'];
            } elseif (isset($item['url'])) {
                $preg = $item['url'] . ($item['url'] != '/' ? '/*' : '');
            }
            if (!empty($preg)) {
                $preg = '/^' . str_replace('*', '(.*?)', str_replace('/', '\/', $preg)) . '$/is';
                $res = (preg_match($preg, Yii::$app->request->url) || preg_match($preg, Yii::$app->request->url . '/'));
            }
        }
        return $res;
    }
}