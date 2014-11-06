<?php

namespace maddoger\website\backend\controllers;

use maddoger\core\i18n\I18N;
use maddoger\website\backend\models\PageSearch;
use maddoger\website\backend\Module;
use maddoger\website\common\models\Page;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
            foreach (I18N::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language['locale']);
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

                Yii::$app->session->addFlash('success', Yii::t('maddoger/website', 'Saved.'));
                switch (Yii::$app->request->post('redirect')) {
                    case 'exit':
                        return $this->redirect(['index']);
                    case 'new':
                        return $this->redirect(['create']);
                    default:
                        return $this->redirect(['view', 'id' => $model->id]);
                }
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
            foreach (I18N::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language['locale']);
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

                Yii::$app->session->addFlash('success', Yii::t('maddoger/website', 'Saved.'));
                switch (Yii::$app->request->post('redirect')) {
                    case 'exit':
                        return $this->redirect(['index']);
                    case 'new':
                        return $this->redirect(['create']);
                    default:
                        return $this->redirect(['view', 'id' => $model->id]);
                }
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
     * @param string $status
     * @return mixed
     */
    public function actionStatus($id, $status)
    {
        $this->findModel($id)->updateAttributes(['status' => $status]);
        if (Yii::$app->request->isAjax) {
            return 'ok';
        } else {
            return $this->redirect(['view', 'id' => $id]);
        }
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
