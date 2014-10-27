<?php

namespace maddoger\website\frontend\controllers;

use maddoger\website\common\models\Page;
use maddoger\website\frontend\Module;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{
    public function actionIndex($slug, $language = null)
    {
        /**
         * @var Page $page
         */
        $pageClass = Module::getInstance()->pageModelClass;
        $page = $pageClass::findBySlug($slug);

        if ($language) {
            Yii::$app->language = $language;
        } else {
            $language = Yii::$app->language;
        }

        if (!$page) {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page "{url}" not found.', ['url' => $slug]));
        }

        $page->setLanguage($language);
        if (!$page->hasTranslation() && $page->default_language) {
            $page->setLanguage($page->default_language);
        }
        if (!$page->hasTranslation()) {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page "{url}" not found.', ['url' => $slug]));
        }

        switch ($page->status) {
            case Page::STATUS_ACTIVE:
                break;

            case Page::STATUS_AUTH_ONLY:
                if (Yii::$app->user->isGuest()) {
                    throw new ForbiddenHttpException(Yii::t('maddoger/website',
                        'You must be authenticated to view this page.'));
                }
                break;

            default:
                throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page "{url}" not found.',
                    ['url' => $slug]));
        }

        if ($page->layout !== null && !empty($page->layout)) {
            $this->layout = $page->layout;
        } else {
            /**
             * @var $this ->module WebsiteModule
             */
            $this->layout = Module::getInstance()->defaultLayout ?: null;
        }

        return $this->render(Module::getInstance()->pageView, ['model' => $page]);
    }
}