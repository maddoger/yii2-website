<?php

namespace rusporting\website\models;

use rusporting\core\ActiveRecord;
use Yii;

/**
 * This is the model class for table "website_menu".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $link
 * @property string $preg
 * @property string $title
 * @property string $css_class
 * @property boolean $enabled
 * @property integer $sort
 * @property integer $create_time
 * @property integer $create_user_id
 * @property integer $update_time
 * @property integer $update_user_id
 *
 * @property Menu $parent
 * @property Menu[] $menus
 */
class Menu extends ActiveRecord
{
	public static function tableName()
	{
		return Yii::$app->db->tablePrefix.'website_menu';
	}

	public function behaviors()
	{
		return [
			'timestamp' => ['class' => 'rusporting\core\behaviors\AutoTimestamp'],
			'user' => ['class' => 'rusporting\core\behaviors\AutoUser'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['parent_id', 'sort'], 'integer'],

			[['title'], 'required'],
			[['enabled'], 'boolean'],
			[['link', 'preg', 'title', 'css_class'], 'string', 'max' => 150]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('rusporting/website', 'ID'),
			'parent_id' => Yii::t('rusporting/website', 'Parent ID'),
			'link' => Yii::t('rusporting/website', 'Link'),
			'preg' => Yii::t('rusporting/website', 'Preg'),
			'title' => Yii::t('rusporting/website', 'Title'),
			'css_class' => Yii::t('rusporting/website', 'CSS Class'),
			'enabled' => Yii::t('rusporting/website', 'Enabled'),
			'sort' => Yii::t('rusporting/website', 'Sort'),
			'create_time' => Yii::t('rusporting/website', 'Create Time'),
			'create_user_id' => Yii::t('rusporting/website', 'Create User ID'),
			'update_time' => Yii::t('rusporting/website', 'Update Time'),
			'update_user_id' => Yii::t('rusporting/website', 'Update User ID'),
		];
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getParent()
	{
		return $this->hasOne(Menu::className(), ['id' => 'parent_id']);
	}

	/**
	 * @return \yii\db\ActiveRelation
	 */
	public function getChildren()
	{
		return $this->hasMany(Menu::className(), ['parent_id' => 'id']);
	}

	/**
	 * Returns array tree from parent (without itself).
	 * Children are in children field.
	 * @param int $parentId
	 * @return null|array
	 */
	public static function getTreeByParentId($parentId=0)
	{
		static $items = null;
		if ($items === null) {
			$items = array(0=> array('children' => array()));

			$models = self::find()->orderBy('sort')->asArray()->all();
			foreach ($models as $item) {
				if ($item === null) break;

				$item['children'] = array();
				$items[$item['id']] = $item;

				if ($item['parent_id'] === null) {
					$parent = &$items[0];
				} else {
					$parent = &$items[$item['parent_id']];
				}

				if ($parent !== null) {
					$parent['children'][$item['id']] = &$items[$item['id']];
				}
			}
		}

		if (isset($items[$parentId])) {
			return $items[$parentId]['children'];
		} else {
			return null;
		}
	}
}
