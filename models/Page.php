<?php

namespace rusporting\website\models;

use Yii;
use rusporting\core\ActiveRecord;

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
 * @property integer $create_time
 * @property integer $create_user_id
 * @property integer $update_time
 * @property integer $update_user_id
 */
class Page extends ActiveRecord
{
	public static function tableName()
	{
		return Yii::$app->db->tablePrefix.'website_page';
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
			[['slug', 'title', 'text'], 'required'],
			[['published'], 'integer'],
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
			'id' => Yii::t('rusporting/website', 'ID'),
			'slug' => Yii::t('rusporting/website', 'Slug'),
			'locale' => Yii::t('rusporting/website', 'Locale'),
			'published' => Yii::t('rusporting/website', 'Publication'),
			'title' => Yii::t('rusporting/website', 'Title'),
			'window_title' => Yii::t('rusporting/website', 'Window Title'),
			'text' => Yii::t('rusporting/website', 'Text'),
			'meta_keywords' => Yii::t('rusporting/website', 'Meta Keywords'),
			'meta_description' => Yii::t('rusporting/website', 'Meta Description'),
			'layout' => Yii::t('rusporting/website', 'Layout'),
			'create_time' => Yii::t('rusporting/website', 'Create Time'),
			'create_user_id' => Yii::t('rusporting/website', 'Create User ID'),
			'update_time' => Yii::t('rusporting/website', 'Update Time'),
			'update_user_id' => Yii::t('rusporting/website', 'Update User ID'),
		];
	}

	public static function findBySlug($slug)
	{
		return static::find(['slug' => $slug]);
	}
}
