<?php

namespace maddoger\website\models;

use Yii;
use maddoger\core\ActiveRecord;

/**
 * This is the model class for table "tbl_website_page".
 *
 * @property integer $id
 * @property string $slug
 * @property string $locale
 * @property integer $published
 * @property string $title
 * @property string $window_title
 * @property string $text
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $layout
 * @property integer $created_at
 * @property integer $created_by_user_id
 * @property integer $updated_at
 * @property integer $updated_by_user_id
 */
class Page extends ActiveRecord
{
	public static function tableName()
	{
		return Yii::$app->db->tablePrefix.'website_page';
	}

	public function init()
	{
		parent::init();
		$this->setAttribute('published', 3);
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
			[['slug', 'title', 'text'], 'required'],
			[['published'], 'integer'],
			[['published'], 'default', 'value'=>3],
			[['text', 'layout'], 'string'],
			[['title'], 'string', 'max' => 50],
			[['slug', 'window_title'], 'string', 'max' => 150],
			[['locale'], 'string', 'max' => 10],
			[['meta_keywords', 'meta_description'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('maddoger/website', 'ID'),
			'slug' => Yii::t('maddoger/website', 'Slug'),
			'locale' => Yii::t('maddoger/website', 'Locale'),
			'published' => Yii::t('maddoger/website', 'Publication'),
			'title' => Yii::t('maddoger/website', 'Title'),
			'window_title' => Yii::t('maddoger/website', 'Window Title'),
			'text' => Yii::t('maddoger/website', 'Text'),
			'meta_keywords' => Yii::t('maddoger/website', 'Meta Keywords'),
			'meta_description' => Yii::t('maddoger/website', 'Meta Description'),
			'layout' => Yii::t('maddoger/website', 'Layout'),
			'created_at' => Yii::t('maddoger/website', 'Create Time'),
			'created_by_user_id' => Yii::t('maddoger/website', 'Create User ID'),
			'updated_at' => Yii::t('maddoger/website', 'Update Time'),
			'updated_by_user_id' => Yii::t('maddoger/website', 'Update User ID'),
		];
	}

	/**
	 * @param $slug
	 * @return null|Page
	 */
	public static function findBySlug($slug)
	{
		return static::find(['slug' => $slug]);
	}

	public static function publishListValues()
	{
		return [
			0 => Yii::t('maddoger/website', 'Unpublished'),
			1 => Yii::t('maddoger/website', 'Only for administrators'),
			2 => Yii::t('maddoger/website', 'Only for authorized users'),
			3 => Yii::t('maddoger/website', 'For all'),
		];
	}

	public function getPublishedValue()
	{
		static $values = null;
		if ($values === null) $values = static::publishListValues();
		return $values[$this->published];
	}
}
