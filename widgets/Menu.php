<?php

namespace rusporting\website\widgets;

use rusporting\website\models\Menu as MenuModel;
use yii\helpers\Html;
use yii\widgets\Menu as BaseMenu;
use Yii;

/**
 * Class Menu
 * Menu with fa icons support
 *
 * @package rusporting\admin\widgets
 */
class Menu extends BaseMenu
{
	/**
	 * @var int|string Parent id or name for items
	 */
	public $parent = null;

	/**
	 * @var null|array Array of items
	 */
	public $items = null;

	/**
	 * @inheritdoc
	 */
	public $linkTemplate = '<a href="{url}"><span>{label}</span></a>';

	/**
	 * @var string the template used to render the body of a menu which is NOT a link.
	 * In this template, the token `{label}` will be replaced with the label of the menu item.
	 * This property will be overridden by the `template` option set in individual menu items via [[items]].
	 */
	public $labelTemplate = '<span>{label}</span>';

	/**
	 * @inheritdoc
	 */
	public $activateParents = true;

	/**
	 * @inheritdoc
	 */
	public $hideEmptyItems = false;

	/**
	 * Renders the menu.
	 */
	public function run()
	{
		if ($this->items === null) {
			if ($this->parent !== null) {
				//Getting items by parent
				if (is_numeric($this->parent)) {
					$children = MenuModel::getTreeByParentId(intval($this->parent));
				} else {
					$children = MenuModel::getTreeByParentTitle($this->parent);
				}
			} else {
				return null;
			}

			if ($children) {
				$this->items = $children;
			}
		}
		parent::run();
	}

	/**
	 * Normalizes the [[items]] property to remove invisible items and activate certain items.
	 * @param array $items the items to be normalized.
	 * @param boolean $active whether there is an active child menu item.
	 * @return array the normalized menu items
	 */
	protected function normalizeItems($items, &$active)
	{
		foreach ($items as $i => $item) {

			if (isset($item['enabled'])) {
				$item['visible'] = $item['enabled'];
			}
			if (isset($item['title'])) {
				$item['label'] = $item['title'];
			}
			if (isset($item['link'])) {
				$item['url'] = Yii::$app->urlManager->baseUrl .$item['link'];
			}
			if (isset($item['children']) && (count($item['children'])>0)) {
				$item['items'] = &$item['children'];
			}
			$items[$i] = $item;

			if (isset($item['visible']) && !$item['visible']) {
				unset($items[$i]);
				continue;
			}

			if (!isset($item['label'])) {
				$item['label'] = '';
			}
			if ($this->encodeLabels) {
				$items[$i]['label'] = Html::encode($item['label']);
			}
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
		$preg = null;
		if (isset($item['preg']) && !empty($item['preg'])) {
			$preg = Yii::$app->urlManager->baseUrl . $item['preg'];
		} else {
			$preg = '/' . trim($item['url'], '/') . ($item['url'] != '/' ? '/*' : '');
		}

		if ($preg !== null && !empty($preg)) {

			$route = '/'.trim(Yii::$app->request->url, '/') . '/';

			$preg = '/^'.str_replace('*', '(.*?)',  str_replace('/', '\/', $preg)).'$/is';

			return preg_match($preg, $route);
		} else {
			return parent::isItemActive($item);
		}
	}
}