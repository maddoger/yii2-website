<?php

namespace maddoger\website\backend\models;

use maddoger\website\backend\Module;
use maddoger\website\common\models\Page;
use maddoger\website\common\models\PageI18n;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PageSearch represents the model behind the search form about `maddoger\website\common\models\Page`.
 */
class PageSearch extends Page
{
    public $title;
    public $language;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['slug', 'layout', 'default_language', 'title', 'language'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $pageClass = Module::getInstance()->pageModelClass;
        /**
         * @var \yii\db\ActiveQuery $query
         */
        $query = $pageClass::find();
        $query->joinWith('translations');
        $query->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->defaultOrder = ['slug' => SORT_ASC];
        $dataProvider->sort->attributes['title'] = [
            'asc' => [PageI18n::tableName() . '.[[title]]' => SORT_ASC],
            'desc' => [PageI18n::tableName() . '.[[title]]' => SORT_DESC],
            'default' => SORT_ASC,
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->title) {
            $query->andWhere([
                'or',
                ['like', PageI18n::tableName() . '.[[title]]', $this->title],
                ['like', PageI18n::tableName() . '.[[language]]', $this->title],
            ]);
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'layout', $this->layout])
            ->andFilterWhere(['like', 'default_language', $this->default_language]);

        return $dataProvider;
    }
}
