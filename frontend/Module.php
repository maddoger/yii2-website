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
}