<?php

namespace maddoger\website\common\models;

use maddoger\website\backend\Module;
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
 * @property string $text_format
 * @property string $text_source
 * @property string $text
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $meta_data
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
    public function formName()
    {
        $name = parent::formName();
        if ($this->language) {
            $name .= '_' . $this->language;
        }
        return $name;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language', 'title', 'text_source'], 'required'],
            [['page_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['text', 'text_source'], 'string'],
            [['text_format'], 'string', 'max' => 10],
            [['language'], 'string', 'max' => 10],
            [['title', 'window_title'], 'string', 'max' => 150],
            [['meta_keywords', 'meta_description'], 'string', 'max' => 255],
            ['meta_data', 'safe'],
            [['window_title', 'meta_keywords', 'meta_description', 'meta_data'], 'default', 'value' => null],
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
            'window_title' => Yii::t('maddoger/website', 'SEO: Title'),
            'text_source' => Yii::t('maddoger/website', 'Text'),
            'text' => Yii::t('maddoger/website', 'Text'),
            'text_format' => Yii::t('maddoger/website', 'Text format'),
            'meta_keywords' => Yii::t('maddoger/website', 'SEO: Keywords'),
            'meta_description' => Yii::t('maddoger/website', 'SEO: Description'),
            'meta_data' => Yii::t('maddoger/website', 'Custom fields'),
            'created_at' => Yii::t('maddoger/website', 'Created At'),
            'created_by' => Yii::t('maddoger/website', 'Created By'),
            'updated_at' => Yii::t('maddoger/website', 'Updated At'),
            'updated_by' => Yii::t('maddoger/website', 'Updated By'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        try {
            $this->meta_data = unserialize($this->meta_data);
        } catch (\Exception $e) {
            $this->meta_data = null;
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isAttributeChanged('text_source') || $this->isAttributeChanged('text_format')) {
            $this->text = $this->getFormattedText();
        }
        if ($this->meta_data) {
            try {
                $this->meta_data = serialize($this->meta_data);
            } catch (\Exception $e) {
                $this->meta_data = null;
            }
        } else {
            $this->meta_data = null;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->meta_data) {
            try {
                $this->meta_data = unserialize($this->meta_data);
            } catch (\Exception $e) {
                $this->meta_data = null;
            }
        }
    }

    /**
     * @return array
     */
    public function getTextFormatInfo()
    {
        return Module::getInstance()->getTextFormatInfo($this->text_format);
    }

    /**
     * @return string
     */
    public function getFormattedText()
    {
        return Module::getInstance()->getFormattedText($this->text_format, $this->text_source, $this->language);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }
}
