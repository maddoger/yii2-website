<?php

namespace maddoger\website\common\models;

use maddoger\core\i18n\TranslatableBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%website_page}}".
 *
 * @property integer $id
 * @property string $slug
 * @property integer $status
 * @property string $layout
 * @property string $default_language
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * I18N
 * @property string $language
 * @property string $title
 * @property string $window_title
 * @property string $text
 * @property string $meta_keywords
 * @property string $meta_description
 *
 * @property Menu[] $Menus
 * @property PageI18n[] $PageI18ns
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%website_page}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'i18n' => [
                'class' => TranslatableBehavior::className(),
                'translationAttributes' => [
                    'title', 'window_title', 'text', 'meta_keywords', 'meta_description',
                ],
            ],
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
            [['slug'], 'required'],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['slug'], 'string', 'max' => 150],
            [['layout'], 'string', 'max' => 50],
            [['default_language'], 'string', 'max' => 10]
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
            'status' => Yii::t('maddoger/website', 'Status'),
            'layout' => Yii::t('maddoger/website', 'Layout'),
            'default_language' => Yii::t('maddoger/website', 'Default Language'),
            'created_at' => Yii::t('maddoger/website', 'Created At'),
            'created_by' => Yii::t('maddoger/website', 'Created By'),
            'updated_at' => Yii::t('maddoger/website', 'Updated At'),
            'updated_by' => Yii::t('maddoger/website', 'Updated By'),

            //I18N
            'language' => Yii::t('maddoger/website', 'Language'),
            'title' => Yii::t('maddoger/website', 'Title'),
            'window_title' => Yii::t('maddoger/website', 'Window Title'),
            'text' => Yii::t('maddoger/website', 'Text'),
            'meta_keywords' => Yii::t('maddoger/website', 'Meta Keywords'),
            'meta_description' => Yii::t('maddoger/website', 'Meta Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenus()
    {
        return $this->hasMany(Menu::className(), ['page_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(PageI18n::className(), ['page_id' => 'id']);
    }
}
