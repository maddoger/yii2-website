<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\frontend\actions;

use maddoger\core\i18n\I18N;
use maddoger\website\common\models\Page;
use maddoger\website\frontend\Module;
use Yii;
use yii\base\Action;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * FixedPageAction
 *
 * This action allows to view pages from Website module.
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */
class FixedPageAction extends Action
{
    /**
     * @var string the view file to be rendered. If not set, Module::$pageView will be used.
     */
    public $view;

    /**
     * @var mixed Page ID or slug
     */
    public $page;

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


    public function run()
    {
        /**
         * @var Page $page
         */
        if (!Module::getInstance()) {
            if (!Yii::$app->getModule($this->moduleId)) {
                throw new ErrorException('Module "'.$this->moduleId.'" not found.');
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
        if (!$language) {
            $language = I18N::getCurrentLanguage();
        }

        Yii::$app->language = $language['locale'];

        if (!$page) {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page not found.'));
        }

        $page->setLanguage($language['locale']);
        if (!$page->hasTranslation() && $page->default_language) {
            $page->setLanguage($page->default_language);
        }
        if (!$page->hasTranslation()) {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page not found.'));
        }

        switch ($page->status) {
            case Page::STATUS_ACTIVE:
                break;

            case Page::STATUS_AUTH_ONLY:
                if (Yii::$app->user->getIsGuest()) {
                    throw new ForbiddenHttpException(Yii::t('maddoger/website',
                        'You must be authenticated to view this page.'));
                }
                break;

            case Page::STATUS_HIDDEN:
                if (!Yii::$app->user->getIsGuest() &&
                    Yii::$app->user->can('website.page.viewHiddenPages')
                ) {
                    break;
                }
                throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page not found.'));

            default:
                throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page not found.'));
        }

        if ($page->layout !== null && !empty($page->layout)) {
            $this->controller->layout = $page->layout;
        } else {
            /**
             * @var $this ->module WebsiteModule
             */
            if (Module::getInstance()->config->defaultLayout) {
                $this->controller->layout = Module::getInstance()->config->defaultLayout;
            }
        }

        return $this->controller->render(
            $this->view ?: Module::getInstance()->pageView,
            ['model' => $page]);
    }
}