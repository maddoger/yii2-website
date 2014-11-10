<?php

namespace maddoger\website\common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%website_menu}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $type
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
                ['parent_id', 'type', 'status', 'sort', 'page_id', 'created_at', 'created_by', 'updated_at', 'updated_by'],
                'integer'
            ],
            [['title'], 'required'],
            [['title', 'link', 'preg'], 'string', 'max' => 150],
            [['target', 'css_class', 'element_id', 'slug'], 'string', 'max' => 50],
            [['language'], 'string', 'max' => 10],
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
            'title' => Yii::t('maddoger/website', 'Title'),
            'link' => Yii::t('maddoger/website', 'Link'),
            'preg' => Yii::t('maddoger/website', 'Preg'),
            'target' => Yii::t('maddoger/website', 'Target'),
            'css_class' => Yii::t('maddoger/website', 'CSS Class'),
            'icon_class' => Yii::t('maddoger/website', 'Icon Class'),
            'element_id' => Yii::t('maddoger/website', 'Element ID'),
            'status' => Yii::t('maddoger/website', 'status'),
            'sort' => Yii::t('maddoger/website', 'Sort'),
            'page_id' => Yii::t('maddoger/website', 'Page ID'),
            'created_at' => Yii::t('maddoger/website', 'Created At'),
            'created_by' => Yii::t('maddoger/website', 'Created By'),
            'updated_at' => Yii::t('maddoger/website', 'Updated At'),
            'updated_by' => Yii::t('maddoger/website', 'Updated By'),
        ];
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
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete(static::$cacheKey);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->cache->delete(static::$cacheKey);
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
        return $this->hasMany(Menu::className(), ['parent_id' => 'id'])->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->with(['children']);
    }

    /**
     * @return array[]
     */
    public function getChildrenItems()
    {
        return static::getTreeByParentId($this->id);
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
    public static function getTreeByParentId($parentId = 0, $cacheDuration = null)
    {
        $cacheDuration = $cacheDuration!==null ?$cacheDuration: static::$cacheDuration;

        $items = Yii::$app->cache->get(static::$cacheKey);
        if ($items === false || $cacheDuration === false) {

            $items = array(0 => array('children' => array()));

            $models = static::find()->orderBy(['sort' => SORT_ASC])->asArray()->all();
            foreach ($models as $item) {
                if ($item === null) {
                    break;
                }

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

            if ($items && $cacheDuration !== false) {
                Yii::$app->cache->set(static::$cacheKey, $items, $cacheDuration);
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

            $model = static::find()->where(['title' => $parentTitle])->select(['id'])->limit(1)->one();
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
