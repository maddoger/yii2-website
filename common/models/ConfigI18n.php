<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\common\models;

use maddoger\core\config\ConfigModel;
use Yii;

/**
 * Config
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */
class ConfigI18n extends ConfigModel
{
    /**
     * @var string
     */
    public $language;

    /**
     * @var string Title of all website
     */
    public $title;

    /**
     * @var string Keywords of all website
     */
    public $meta_keywords;

    /**
     * @var string Description of all website
     */
    public $meta_description;

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
            [['title'], 'string', 'max' => 150],
            [['meta_keywords', 'meta_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language' => Yii::t('maddoger/website', 'Language'),
            'title' => Yii::t('maddoger/website', 'SEO: Title'),
            'meta_keywords' => Yii::t('maddoger/website', 'SEO: Keywords'),
            'meta_description' => Yii::t('maddoger/website', 'SEO: Description'),
        ];
    }
}