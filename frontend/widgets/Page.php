<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\frontend\widgets;

use maddoger\core\i18n\I18N;
use maddoger\website\common\models\Page as PageModel;
use maddoger\website\frontend\Module;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Page
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */
class Page extends Widget
{
    /**
     * @var mixed Page ID or slug
     */
    public $page;

    /**
     * @var array
     */
    public $options = ['tag' => 'div'];

    /**
     * @var string the view file to be rendered. If not set, simple text will be used.
     */
    public $view;

    /**
     * @var string default language, if language slug is not set. If null, application language will be used.
     *
     * Example: ru-RU
     */
    public $language;

    /**
     * @var string
     */
    public $moduleId = 'website';

    /**
     * @var bool
     */
    public $title = true;

    /**
     * @var array
     */
    public $titleOptions = ['tag' => 'h2'];

    /**
     * @return string
     * @throws ErrorException
     */
    public function run()
    {
        /**
         * @var PageModel $page
         */
        if (!Module::getInstance()) {
            if (!Yii::$app->getModule($this->moduleId)) {
                throw new ErrorException('Module "' . $this->moduleId . '" not found.');
            }
        }

        $pageClass = Module::getInstance()->pageModelClass;
        if (is_int($this->page)) {
            $page = $pageClass::findOne($this->page);
        } elseif (is_string($this->page)) {
            $page = $pageClass::findBySlug($this->page);
        } else {
            throw new InvalidParamException('Invalid page identifier.');
        }

        $language = $this->language;
        if ($language) {
            if (!is_array($language)) {
                $language = I18N::getLanguageByLocale($language);
            }
        } else {
            $language = I18N::getCurrentLanguage();
        }

        if (!$page) {
            return null;
        }

        $page->setLanguage($language['locale']);
        if (!$page->hasTranslation() && $page->default_language) {
            $page->setLanguage($page->default_language);
        }
        if (!$page->hasTranslation()) {
            return null;
        }

        $content = $this->view ? $this->render($this->view, ['model' => $page]) : $page->text;
        if ($this->title) {
            $options = $this->titleOptions;
            $tag = ArrayHelper::remove($options, 'tag', 'h2');
            $content = Html::tag($tag, is_string($this->title) ? $this->title : $page->title, $options) . $content;
        }
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        return Html::tag($tag, $content, $options);
    }
}