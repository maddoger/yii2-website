<?php

namespace maddoger\website\models;

use maddoger\core\ActiveRecord;
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
 * @property string $element_id
 * @property boolean $enabled
 * @property integer $sort
 * @property integer $created_at
 * @property integer $created_by_user_id
 * @property integer $updated_at
 * @property integer $updated_by_user_id
 *
 * @property Menu $parentTitle
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
			'timestamp' => ['class' => 'maddoger\core\behaviors\AutoTimestamp'],
			'user' => ['class' => 'maddoger\core\behaviors\AutoUser'],
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
			[['link', 'preg', 'title', 'css_class', 'element_id'], 'string', 'max' => 150]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('maddoger/website', 'ID'),
			'parent_id' => Yii::t('maddoger/website', 'Parent ID'),
			'link' => Yii::t('maddoger/website', 'Link'),
			'preg' => Yii::t('maddoger/website', 'Preg'),
			'title' => Yii::t('maddoger/website', 'Title'),
			'css_class' => Yii::t('maddoger/website', 'CSS Class'),
			'element_id' => Yii::t('maddoger/website', 'Element ID'),
			'enabled' => Yii::t('maddoger/website', 'Enabled'),
			'sort' => Yii::t('maddoger/website', 'Sort'),
			'created_at' => Yii::t('maddoger/website', 'Create Time'),
			'created_by_user_id' => Yii::t('maddoger/website', 'Create User ID'),
			'updated_at' => Yii::t('maddoger/website', 'Update Time'),
			'updated_by_user_id' => Yii::t('maddoger/website', 'Update User ID'),
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
	 * Returns array tree from parentTitle (without itself).
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

	/**
	 * Returns array tree from parentTitle (without itself).
	 * Children are in children field.
	 *
	 * @param string $parentTitle
	 * @return null|array
	 */
	public static function getTreeByParentTitle($parentTitle)
	{
		static $itemsNameToId = null;
		if ($itemsNameToId === null) {
			$itemsNameToId = array();
		}

		$parentId = null;

		if (isset($itemsNameToId[$parentTitle])) {
			$parentId = $itemsNameToId[$parentTitle];
		} else {

			$model = self::find()->where(['title' => $parentTitle])->select(['id'])->limit(1)->one();
			if ($model) {
				$parentId = $model->id;
				$itemsNameToId[$parentTitle] = $parentId;
			}
		}

		if ($parentId !== null) {
			return static::getTreeByParentId($parentId);
		} else {
			return null;
		}
	}
}
