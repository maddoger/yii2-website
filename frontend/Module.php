<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\frontend;

use maddoger\core\config\ConfigBehavior;
use Yii;
use yii\log\Logger;

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
     * @var string view file path
     */
    public $pageView = '@maddoger/website/frontend/views/page/index.php';

    /**
     * Init module
     */
    public function init()
    {
        Yii::getLogger()->log('BEFORE_INIT', Logger::LEVEL_INFO);
        parent::init();
        Yii::getLogger()->log('AFTER_INIT', Logger::LEVEL_INFO);

        if (!isset(Yii::$app->i18n->translations['maddoger/website'])) {

            Yii::$app->i18n->translations['maddoger/website'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@maddoger/website/common/messages',
                'sourceLanguage' => 'en-US',
            ];
        }
    }

    public function behaviors()
    {
        return [
            [
                'class' => ConfigBehavior::className(),
            ],
        ];
    }
}