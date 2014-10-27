<?php

namespace maddoger\website\backend\controllers;

use maddoger\website\backend\Module;
use Yii;
use maddoger\website\common\models\Page;
use maddoger\website\backend\models\PageSearch;
use maddoger\website\frontend\Module as FrontendModule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionFaker($count=1)
    {
        $languages = FrontendModule::getAvailableLanguages();
        /**
         * @var \Faker\Generator[] $fakers
         */
        $fakers = [];
        $faker = null;
        foreach ($languages as $language) {
            $faker = \Faker\Factory::create(str_replace('-', '_', $language));
            $fakers[$language] = $faker;
        }

        for($i=0; $i<$count; $i++) {
            $page = new Page();

            $page->status = 10;
            $page->slug = implode('-', $faker->words(rand(1, 3)));

            foreach ($languages as $language) {

                $page->setLanguage($language);
                $page->title = $fakers[$language]->colorName.' - '.$fakers[$language]->sentence();
                $page->window_title = $fakers[$language]->sentence();
                $page->text = $fakers[$language]->realText(2000);
                $page->meta_keywords = implode(', ', $faker->words(10));
                $page->meta_description = implode(', ', $faker->sentences(3));

                $page->save();
            }

            //var_dump($page);
        }

        return $this->redirect(['index']);
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Page model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $pageClass = Module::getInstance()->pageModelClass;
        /**
         * @var \maddoger\website\common\models\Page $model
         */
        $model = new $pageClass();

        if ($model->load(Yii::$app->request->post())) {

            $validate = true;
            foreach (FrontendModule::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language);
                if ($modelI18n->load(Yii::$app->request->post())) {
                    if (empty($modelI18n->title) && empty($modelI18n->text)) {
                        if (!$modelI18n->isNewRecord) {
                            $modelI18n->delete();
                        }
                    } elseif (!$modelI18n->validate()) {
                        $validate = false;
                    }
                }
            }

            if ($validate && $model->save()) {

                Yii::$app->session->addFlash('success', 'Сохранено!');
                switch (Yii::$app->request->post('redirect')) {
                    case 'exit':
                        return $this->redirect(['index']);
                    case 'new':
                        return $this->redirect(['create']);
                    default:
                        return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->addFlash('error', 'Ошибка!');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $validate = true;
            foreach (FrontendModule::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language);
                if ($modelI18n->load(Yii::$app->request->post())) {
                    if (empty($modelI18n->title) && empty($modelI18n->text)) {
                        if (!$modelI18n->isNewRecord) {
                            $modelI18n->delete();
                        }
                    } elseif (!$modelI18n->validate()) {
                        $validate = false;
                    }
                }
            }

            if ($validate && $model->save()) {

                Yii::$app->session->addFlash('success', 'Сохранено!');
                switch (Yii::$app->request->post('redirect')) {
                    case 'exit':
                        return $this->redirect(['index']);
                    case 'new':
                        return $this->redirect(['create']);
                    default:
                        return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->addFlash('error', 'Ошибка!');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $pageClass = Module::getInstance()->pageModelClass;
        if (($model = $pageClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'The requested page does not exist.'));
        }
    }
}
