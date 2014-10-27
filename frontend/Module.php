<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\frontend;
use Yii;

/**
 * WebsiteModule
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */
class Module extends \yii\base\Module
{
    /**
     * @var string page model class
     */
    public $pageModelClass = 'maddoger\website\common\models\Page';

    /**
     * @var string Title of all website
     */
    public $title;

    /**
     * @var string Keywords of all website
     */
    public $keywords;

    /**
     * @var string Description of all website
     */
    public $description;

    /**
     * @var array available layouts for pages
     */
    public $layouts;

    /**
     * @var string default layout
     */
    public $defaultLayout;

    /**
     * @var string scripts will be added to the end of body tag
     */
    public $endBodyScripts;

    /**
     * @var array available languages
     */
    static private $_availableLanguages;

    /**
     * Init module
     */
    public function init()
    {
        parent::init();

        if (!isset(Yii::$app->i18n->translations['maddoger/website'])) {

            Yii::$app->i18n->translations['maddoger/website'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@maddoger/website/common/messages',
                'sourceLanguage' => 'en-US',
            ];
        }
    }

    /**
     * @return array
     */
    public static function getAvailableLanguages()
    {
        if (!static::$_availableLanguages) {
            if (isset(Yii::$app->params['availableLanguages'])
                && Yii::$app->params['availableLanguages']) {
                static::$_availableLanguages = Yii::$app->params['availableLanguages'];
                //sort(static::$_availableLanguages);
            } else {
                static::$_availableLanguages = [Yii::$app->language];
            }
        }
        return static::$_availableLanguages;
    }

    /**
     * @param $value
     */
    public static function setAvailableLanguages($value)
    {
        static::$_availableLanguages = $value;
    }
}