<?php

namespace maddoger\website\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use maddoger\website\models\Page;

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
			'id' => \Yii::t('maddoger/website', 'ID'),
			'slug' => \Yii::t('maddoger/website', 'Slug'),
			'locale' => \Yii::t('maddoger/website', 'Locale'),
			'published' => \Yii::t('maddoger/website', 'Published'),
			'title' => \Yii::t('maddoger/website', 'Title'),
			'window_title' => \Yii::t('maddoger/website', 'Window Title'),
			'text' => \Yii::t('maddoger/website', 'Text'),
			'meta_keywords' => \Yii::t('maddoger/website', 'Meta Keywords'),
			'meta_description' => \Yii::t('maddoger/website', 'Meta Description'),
			'created_at' => \Yii::t('maddoger/website', 'Create Time'),
			'created_by_user_id' => \Yii::t('maddoger/website', 'Create User ID'),
			'updated_at' => \Yii::t('maddoger/website', 'Update Time'),
			'updated_by_user_id' => \Yii::t('maddoger/website', 'Update User ID'),
		];
	}

	public function search($params)
	{
		$query = Page::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);


		if (!($this->load($params) && $this->validate())) {
			if (count($query->orderBy) == 0) {
				$query->addOrderBy('slug');
			}
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
			$value = strtr($value, ['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']);
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}
