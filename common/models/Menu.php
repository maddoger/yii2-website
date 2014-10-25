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
 * @property string $title
 * @property string $link
 * @property string $preg
 * @property string $target
 * @property string $css_class
 * @property string $element_id
 * @property integer $enabled
 * @property integer $sort
 * @property integer $page_id
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Page $page
 * @property Menu $parent
 * @property Menu[] $menus
 */
class Menu extends \yii\db\ActiveRecord
{
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
            [['parent_id', 'enabled', 'sort', 'page_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'required'],
            [['title', 'link', 'preg'], 'string', 'max' => 150],
            [['target', 'css_class', 'element_id'], 'string', 'max' => 50]
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
            'title' => Yii::t('maddoger/website', 'Title'),
            'link' => Yii::t('maddoger/website', 'Link'),
            'preg' => Yii::t('maddoger/website', 'Preg'),
            'target' => Yii::t('maddoger/website', 'Target'),
            'css_class' => Yii::t('maddoger/website', 'Css Class'),
            'element_id' => Yii::t('maddoger/website', 'Element ID'),
            'enabled' => Yii::t('maddoger/website', 'Enabled'),
            'sort' => Yii::t('maddoger/website', 'Sort'),
            'page_id' => Yii::t('maddoger/website', 'Page ID'),
            'created_at' => Yii::t('maddoger/website', 'Created At'),
            'created_by' => Yii::t('maddoger/website', 'Created By'),
            'updated_at' => Yii::t('maddoger/website', 'Updated At'),
            'updated_by' => Yii::t('maddoger/website', 'Updated By'),
        ];
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
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['parent_id' => 'id']);
    }
}
