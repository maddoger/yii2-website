<?php

namespace maddoger\website\common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\DbDependency;

/**
 * This is the model class for table "{{%website_menu}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $type
 * @property string $label
 * @property string $title
 * @property string $slug
 * @property string $language
 * @property string $link
 * @property string $preg
 * @property string $target
 * @property string $css_class
 * @property string $icon_class
 * @property string $element_id
 * @property integer $status
 * @property integer $sort
 * @property integer $page_id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Page $page
 * @property Menu $parent
 * @property Menu[] $children
 */
class Menu extends \yii\db\ActiveRecord
{
    const TYPE_MENU = 0;
    const TYPE_LINK = 1;
    const TYPE_PAGE = 2;

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @var string Key name for caching menu structure
     */
    public static $cacheKey = 'WEBSITE_MENU';

    /**
     * @var int Menu structure cache duration
     */
    public static $cacheDuration = 3600;

    /**
     * @var \yii\caching\Dependency
     */
    public static $cacheDependencyQuery = 'SELECT MAX([[updated_at]]) FROM {{%website_menu}}';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%website_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'parent_id',
                    'type',
                    'status',
                    'sort',
                    'page_id',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by'
                ],
                'integer'
            ],
            [['label'], 'required'],
            [['label', 'title', 'link', 'preg'], 'string', 'max' => 150],
            [['target', 'css_class', 'icon_class', 'element_id', 'slug'], 'string', 'max' => 50],
            [['language'], 'string', 'max' => 10],
            [['title', 'link', 'language', 'css_class', 'icon_class', 'element_id', 'slug'], 'default', 'value' => null]
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
            'type' => Yii::t('maddoger/website', 'Type'),
            'slug' => Yii::t('maddoger/website', 'Identifier for templates'),
            'label' => Yii::t('maddoger/website', 'Label'),
            'title' => Yii::t('maddoger/website', 'Title attribute'),
            'language' => Yii::t('maddoger/website', 'Language'),
            'link' => Yii::t('maddoger/website', 'Link'),
            'preg' => Yii::t('maddoger/website', 'Preg'),
            'target' => Yii::t('maddoger/website', 'Target'),
            'css_class' => Yii::t('maddoger/website', 'CSS Class'),
            'icon_class' => Yii::t('maddoger/website', 'Icon Class'),
            'element_id' => Yii::t('maddoger/website', 'Element ID'),
            'status' => Yii::t('maddoger/website', 'status'),
            'sort' => Yii::t('maddoger/website', 'Sort'),
            'page_id' => Yii::t('maddoger/website', 'Page'),
            'created_at' => Yii::t('maddoger/website', 'Created At'),
            'created_by' => Yii::t('maddoger/website', 'Created By'),
            'updated_at' => Yii::t('maddoger/website', 'Updated At'),
            'updated_by' => Yii::t('maddoger/website', 'Updated By'),
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['newItem'] = $scenarios['default'];
        $scenarios['updateMenuItems'] = $scenarios['default'];
        return $scenarios;
    }

    public function formName()
    {
        if ($this->scenario == 'newItem') {
            return 'MenuNewItem';
        } elseif ($this->scenario == 'updateMenuItems') {
            return 'menu-items[' . $this->id . ']';
        } else {
            return parent::formName();
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->type === null) {
            $this->type = self::TYPE_LINK;
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert && $this->sort === null) {
            $this->sort = Menu::find()->where(['parent_id' => $this->parent_id])->max('sort') + 1;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Menu::className(), ['parent_id' => 'id'])->orderBy([
            'sort' => SORT_ASC,
            'id' => SORT_ASC
        ])->with(['children']);
    }

    /**
     * @return array[]
     */
    public function getItems()
    {
        return static::getItemsByParentId($this->id);
    }

    /**
     * Status sting representation
     * @return string
     */
    public function getStatusDescription()
    {
        static $list = null;
        if ($list === null) {
            $list = static::getStatusList();
        }
        return (isset($list[$this->status])) ? $list[$this->status] : $this->status;
    }

    /**
     * List of all possible statuses
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_DRAFT => Yii::t('maddoger/website', 'Draft'),
            self::STATUS_ACTIVE => Yii::t('maddoger/website', 'Active'),
        ];
    }

    /**
     * @return array|static[]
     */
    public static function findMenus()
    {
        return static::find()->where(['type' => self::TYPE_MENU])->orderBy(['sort' => SORT_ASC])->indexBy('id')->all();
    }

    /**
     * Returns array tree from parentTitle (without itself).
     * Children are in children field.
     * @param int $parentId
     * @param int|bool $cacheDuration
     * @return null|array
     */
    public static function getItemsByParentId($parentId = 0, $cacheDuration = null)
    {
        $cacheDuration = $cacheDuration !== null ? $cacheDuration : static::$cacheDuration;

        $items = Yii::$app->cache->get(static::$cacheKey);
        //$items = null;
        if (!$items || $cacheDuration === false) {

            $items = static::find()->
            orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])->
            indexBy('id')->
            asArray()->
            all();
            $items[0] = ['items' => []];

            foreach ($items as $id => $item) {
                if (!$id) {
                    continue;
                }
                if ($item['parent_id'] === null) {
                    $item['parent_id'] = 0;
                }
                $items[$id]['url'] = $item['link'];
                $items[$id]['options'] = [];
                if (!empty($item['css_class'])) {
                    $items[$id]['options']['class'] = $item['css_class'];
                }
                if (!empty($item['element_id'])) {
                    $items[$id]['options']['id'] = $item['element_id'];
                }
                if (isset($items[$item['parent_id']])) {
                    $parent = &$items[$item['parent_id']];
                    if (!isset($parent['items'])) {
                        $parent['items'] = [];
                    }
                    $parent['items'][$id] = &$items[$id];
                }
            }

            if ($items && $cacheDuration !== false) {

                $dependency = static::$cacheDependencyQuery ?
                    new DbDependency(['sql' => static::$cacheDependencyQuery]) :
                    null;

                Yii::$app->cache->set(static::$cacheKey, $items, $cacheDuration, $dependency);

            } else {
                Yii::$app->cache->delete(static::$cacheKey);
            }
        }

        if (isset($items[$parentId])) {
            return $items[$parentId]['items'];
        } else {
            return null;
        }
    }

    /**
     * Returns array tree from parentTitle (without itself).
     * Children are in children field.
     *
     * @param string $slug
     * @param string $language
     * @return null|static
     */
    public static function findBySlug($slug, $language = null)
    {
        $menuQuery = static::find()->where(['slug' => $slug]);
        if ($language === null) {
            $language = Yii::$app->language;
        }
        $menuQuery->andWhere(['or', ['language' => $language], ['language' => null]]);
        $menu = $menuQuery->limit(1)->one();

        return $menu;
    }

    /**
     * @param int $parentId
     * @param int $maxLevel
     * @param string $levelDelimiter
     * @return array
     */
    public static function getList($parentId = 0, $maxLevel = null, $levelDelimiter = '- ')
    {
        $tree = static::getItemsByParentId($parentId);
        if (!$tree) {
            return [];
        }
        $res = [];
        $func = function ($items, $level = 0) use (&$res, &$func, &$levelDelimiter, &$maxLevel) {
            foreach ($items as $item) {
                $res[$item['id']] = str_repeat($levelDelimiter, $level) . $item['label'];
                if (isset($item['items']) && !empty($item['items'])) {
                    if ($maxLevel === null || $level < $maxLevel) {
                        $func($item['items'], $level + 1);
                    }
                }
            }
        };
        $func($tree);
        return $res;
    }
}
