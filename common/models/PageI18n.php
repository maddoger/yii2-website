<?php

namespace maddoger\website\common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%website_page_i18n}}".
 *
 * @property integer $id
 * @property integer $page_id
 * @property string $language
 * @property string $title
 * @property string $window_title
 * @property string $text
 * @property string $meta_keywords
 * @property string $meta_description
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Page $page
 */
class PageI18n extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%website_page_i18n}}';
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
            [['page_id', 'language', 'title'], 'required'],
            [['page_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['text'], 'string'],
            [['language'], 'string', 'max' => 10],
            [['title', 'window_title'], 'string', 'max' => 150],
            [['meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [
                ['page_id', 'language'],
                'unique',
                'targetAttribute' => ['page_id', 'language'],
                'message' => 'The combination of Page ID and Language has already been taken.'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('maddoger/website', 'ID'),
            'page_id' => Yii::t('maddoger/website', 'Page ID'),
            'language' => Yii::t('maddoger/website', 'Language'),
            'title' => Yii::t('maddoger/website', 'Title'),
            'window_title' => Yii::t('maddoger/website', 'Window Title'),
            'text' => Yii::t('maddoger/website', 'Text'),
            'meta_keywords' => Yii::t('maddoger/website', 'Meta Keywords'),
            'meta_description' => Yii::t('maddoger/website', 'Meta Description'),
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
}
