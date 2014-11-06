<?php

namespace maddoger\website\common\models;

use maddoger\core\i18n\TranslatableBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;
use yii\helpers\Markdown;

/**
 * This is the model class for table "{{%website_page}}".
 *
 * @method setLanguage($value)
 * @method string getLanguage()
 * @method setTranslationAttribute($attribute, $value)
 * @method mixed getTranslationAttribute($attribute)
 * @method PageI18n getTranslation($language = null)
 * @method bool hasTranslation($language = null)
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
 * @property string $text_format
 * @property string $meta_keywords
 * @property string $meta_description
 *
 * @property Menu[] $menus
 * @property PageI18n[] $translations
 * @property array availableLanguages
 */
class Page extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_HIDDEN = 1;
    const STATUS_AUTH_ONLY = 3;
    const STATUS_ACTIVE = 10;

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
                'defaultLanguageAttribute' => 'default_language',
                'translationAttributes' => [
                    'title',
                    'window_title',
                    'text_format',
                    'text',
                    'meta_keywords',
                    'meta_description',
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
            //[['slug'], 'required'],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['slug'], 'string', 'max' => 150],
            [['layout'], 'string', 'max' => 50],
            [['default_language'], 'string', 'max' => 10],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [
                'status',
                'in',
                'range' => [self::STATUS_DRAFT, self::STATUS_HIDDEN, self::STATUS_AUTH_ONLY, self::STATUS_ACTIVE]
            ],
            [['layout', 'default_language'], 'default', 'value' => null],

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
            'title' => Yii::t('maddoger/website', 'Title'),
            'window_title' => Yii::t('maddoger/website', 'SEO: Title'),
            'text' => Yii::t('maddoger/website', 'Text'),
            'text_format' => Yii::t('maddoger/website', 'Text format'),
            'meta_keywords' => Yii::t('maddoger/website', 'SEO: Keywords'),
            'meta_description' => Yii::t('maddoger/website', 'SEO: Description'),
        ];
    }

    public function beforeValidate()
    {
        if (empty($this->slug)) {
            $title = $this->hasTranslation('en-US') ?
                $this->getTranslation('en-US')->title :
                $this->title;
            $this->slug = Inflector::slug($title);
        }
        if ($this->isAttributeChanged('slug')) {
            $this->slug = trim($this->slug, '/');
        }
        return parent::beforeValidate();
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

    /**
     * @return array
     */
    public function getTranslatedLanguages()
    {
        return $this->getTranslations()->select(['language'])->distinct()->orderBy(['language' => SORT_ASC])->column();
    }

    public function getFormattedText($format = null)
    {
        if (!$format) {
            $format = $this->text_format;
        }
        $text = trim($this->text);
        switch ($format) {
            case 'html':
                return Yii::$app->formatter->asHtml($text);
            case 'text':
                return Yii::$app->formatter->asNtext($text);
            case 'md':
                return Markdown::process($text, 'gfm');

            default:
                return $text;
        }
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
            self::STATUS_HIDDEN => Yii::t('maddoger/website', 'Hidden'),
            self::STATUS_AUTH_ONLY => Yii::t('maddoger/website', 'Auth users only'),
            self::STATUS_ACTIVE => Yii::t('maddoger/website', 'Active'),
        ];
    }

    /**
     * @param $slug
     * @return null|Page
     */
    public static function findBySlug($slug)
    {
        $slug = trim($slug, '/\\');
        $query = static::find();
        $query->with(['translations']);
        $query->andWhere(['or', ['slug' => $slug], ['slug' => '/' . $slug]]);
        $query->limit(1);
        return $query->one();
    }
}
