<?php

namespace rusporting\website\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use rusporting\website\models\Page;

/**
 * PageSearch represents the model behind the search form about Page.
 */
class PageSearch extends Model
{
	public $id;
	public $slug;
	public $locale;
	public $published;
	public $title;
	public $window_title;
	public $text;
	public $meta_keywords;
	public $meta_description;
	public $created_at;
	public $created_by_user_id;
	public $updated_at;
	public $updated_by_user_id;

	public function rules()
	{
		return [
			[['id', 'published', 'created_at', 'created_by_user_id', 'updated_at', 'updated_by_user_id'], 'integer'],
			[['slug', 'locale', 'title', 'window_title', 'text', 'meta_keywords', 'meta_description'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => \Yii::t('rusporting/website', 'ID'),
			'slug' => \Yii::t('rusporting/website', 'Slug'),
			'locale' => \Yii::t('rusporting/website', 'Locale'),
			'published' => \Yii::t('rusporting/website', 'Published'),
			'title' => \Yii::t('rusporting/website', 'Title'),
			'window_title' => \Yii::t('rusporting/website', 'Window Title'),
			'text' => \Yii::t('rusporting/website', 'Text'),
			'meta_keywords' => \Yii::t('rusporting/website', 'Meta Keywords'),
			'meta_description' => \Yii::t('rusporting/website', 'Meta Description'),
			'created_at' => \Yii::t('rusporting/website', 'Create Time'),
			'created_by_user_id' => \Yii::t('rusporting/website', 'Create User ID'),
			'updated_at' => \Yii::t('rusporting/website', 'Update Time'),
			'updated_by_user_id' => \Yii::t('rusporting/website', 'Update User ID'),
		];
	}

	public function search($params)
	{
		$query = Page::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$query->orderBy('slug');


		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'slug', true);
		$this->addCondition($query, 'locale', true);
		$this->addCondition($query, 'published');
		$this->addCondition($query, 'title', true);
		$this->addCondition($query, 'window_title', true);
		$this->addCondition($query, 'text', true);
		$this->addCondition($query, 'meta_keywords', true);
		$this->addCondition($query, 'meta_description', true);
		$this->addCondition($query, 'created_at');
		$this->addCondition($query, 'created_by_user_id');
		$this->addCondition($query, 'updated_at');
		$this->addCondition($query, 'updated_by_user_id');
		return $dataProvider;
	}

	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}
		if ($partialMatch) {
			$value = '%' . strtr($value, ['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']) . '%';
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}
