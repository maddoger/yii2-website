<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\common\models;

use maddoger\core\i18n\TranslatableModelBehavior;
use Yii;
use maddoger\core\config\ConfigModel;

/**
 * Config
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 *
 * @method setLanguage($value)
 * @method string getLanguage()
 * @method setTranslationAttribute($attribute, $value)
 * @method mixed getTranslationAttribute($attribute)
 * @method PageI18n getTranslation($language = null)
 * @method bool hasTranslation($language = null)
 *
 * @property string $title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property string $language
 */
class Config extends ConfigModel
{
    /**
     * @var string default layout
     */
    public $defaultLayout;

    /**
     * @var string scripts will be added to the end of body tag
     */
    public $endBodyScripts;

    /**
     * @var array available layouts for pages
     */
    public $layouts;

    /**
     * @var string default text format
     */
    public $defaultTextFormat;

    /**
     * @var ConfigI18n[]
     */
    public $translations;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TranslatableModelBehavior::className(),
                'translationClass' => '\maddoger\website\common\models\ConfigI18n',
                'translationAttributes' => [
                    'title', 'meta_keywords', 'meta_description',
                ]
            ],
        ];
    }

    /**
     * @param $language
     * @return ConfigI18N
     */
    /*public function getTranslation($language)
    {
        if (!isset($this->translations[$language])) {
           $this->translations[$language] = new ConfigI18n(['language' => $language]);
        }
        return $this->translations[$language];
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['defaultLayout'], 'string', 'max' => 150],
            [['endBodyScripts'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'defaultLayout' => Yii::t('maddoger/website', 'Default layout'),
            'endBodyScripts' => Yii::t('maddoger/website', 'Counters JavaScript'),
        ];
    }
}