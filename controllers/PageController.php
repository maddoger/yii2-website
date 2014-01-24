<?php

namespace rusporting\website\controllers;

use rusporting\core\FrontendController;
use rusporting\website\models\Page;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

class PageController extends FrontendController
{
	public function actionIndex($slug)
	{
		$slug = '/'.trim($slug, '/\\');

		$page = Page::findBySlug($slug);
		if (!$page) {
			throw new NotFoundHttpException(Yii::t('rusporting/website', 'Page "{url}" not found.', ['url' => $slug]));
		}

		switch ($page->published) {
			case 0: //Hiden
				throw new NotFoundHttpException(Yii::t('rusporting/website', 'Page "{url}" not found.', ['url' => $slug]));
				break;
			case 1: //Admin only
				if (!Yii::$app->user->checkAccess('page.read')) {
					throw new NotFoundHttpException(Yii::t('rusporting/website', 'Page "{url}" not found.', ['url' => $slug]));
				}
				break;
			case 2: //Auth only
				if (Yii::$app->user->isGuest()) {
					throw new ForbiddenHttpException(Yii::t('rusporting/website', 'You must be authenticated to view this page.'));
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
			 * @var $this->module WebsiteModule
			 */
			if ($this->module->defaultLayout !== null) {
				$this->layout = $this->module->defaultLayout;
			}
		}

		$layoutFile = $this->findLayoutFile($this->getView());
		if ($this->layout && !file_exists($layoutFile)) {
			$this->layout = '/'.$this->layout;
			$layoutFile = $this->findLayoutFile($this->getView());
		}

		if ($layoutFile !== false) {
			return $this->renderFile($layoutFile, ['content' => $page->text], $this);
		} else {
			return $page->text;
		}
	}
}