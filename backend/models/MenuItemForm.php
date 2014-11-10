<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend\models;
use maddoger\website\common\models\Menu;
use Yii;

/**
 * MenuItemForm
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */
class MenuItemForm extends Menu
{
    public function formName()
    {
        return 'menu-items['.$this->id.']';
    }
}