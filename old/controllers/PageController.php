<?php

namespace maddoger\website\controllers;

use maddoger\core\FrontendController;
use maddoger\website\models\Page;
use Yii;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class PageController extends FrontendController
{
    public function actionIndex($slug)
    {
        $slug = '/' . trim($slug, '/\\');

        $page = Page::findBySlug($slug);
        if (!$page) {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page "{url}" not found.', ['url' => $slug]));
        }

        if (!empty($page->locale)) {
            Yii::$app->language = $page->locale;
        }

        switch ($page->published) {
            case 0: //Hiden
                throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page "{url}" not found.',
                        ['url' => $slug]));
                break;
            case 1: //Admin only
                if (!Yii::$app->user->can('page.read')) {
                    throw new NotFoundHttpException(Yii::t('maddoger/website', 'Page "{url}" not found.',
                            ['url' => $slug]));
                }
                break;
            case 2: //Auth only
                if (Yii::$app->user->isGuest()) {
                    throw new ForbiddenHttpException(Yii::t('maddoger/website',
                            'You must be authenticated to view this page.'));
                }
                break;
        }

        $this->title = $page->title;
        $this->windowTitle = $page->window_title;
        $this->metaKeywords = $page->meta_keywords;
        $this->metaDescription = $page->meta_description;

        if ($page->layout !== null && !empty($page->layout)) {
            $this->layout = $page->layout;
        } else {
            /**
             * @var $this ->module WebsiteModule
             */
            if ($this->module->defaultLayout !== null) {
                $this->layout = $this->module->defaultLayout;
            }
        }

        $layoutFile = $this->findLayoutFile($this->getView());
        if ($this->layout && !file_exists($layoutFile)) {
            $this->layout = '/' . $this->layout;
            $layoutFile = $this->findLayoutFile($this->getView());
        }

        $isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->can('pages.update');
        $content = ($isAdmin) ?
            Html::a(\Yii::t('maddoger/website', 'Edit'), ['/administrator/website/pages/update', 'id' => $page->id],
                ['class' => 'btn-edit']) . $page->text :
            $page->text;

        if ($layoutFile !== false) {
            return $this->renderFile($layoutFile, ['content' => $content], $this);
        } else {
            return $page->text;
        }
    }
}