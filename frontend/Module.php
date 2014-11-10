<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\frontend;

use maddoger\website\common\models\Config;
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
     * @var string view file path
     */
    public $pageView = '@maddoger/website/frontend/views/page/index.php';

    /**
     * @var \maddoger\website\common\models\Config Module configuration
     */
    public $config;

    /**
     * Init module
     */
    public function init()
    {
        parent::init();
        $this->config = Config::getConfig($this->className(),
            is_array($this->config) ? $this->config : []
        );

        if (!isset(Yii::$app->i18n->translations['maddoger/website'])) {

            Yii::$app->i18n->translations['maddoger/website'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@maddoger/website/common/messages',
                'sourceLanguage' => 'en-US',
            ];
        }
    }
}