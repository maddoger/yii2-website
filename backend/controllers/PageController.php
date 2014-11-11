<?php

namespace maddoger\website\backend\controllers;

use maddoger\core\i18n\I18N;
use maddoger\website\backend\models\PageSearch;
use maddoger\website\backend\Module;
use maddoger\website\common\models\Menu;
use maddoger\website\common\models\Page;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'roles' => ['website.page.view'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create'],
                        'roles' => ['website.page.create'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['update'],
                        'roles' => ['website.page.update'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['website.page.delete'],
                        'verbs' => ['POST'],
                    ],
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
        $menus = [new Menu()];

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

                //Update menu items
                $updateMenuItems = Yii::$app->request->post('menu-items-update');
                foreach ($menus as $menu) {
                    if ($menu->isNewRecord) {
                        if (Yii::$app->request->post('menu-items-create')) {
                            $menu->page_id = $model->id;
                            $menu->parent_id = Yii::$app->request->post('menu-items-create-parent_id');
                            if (!$menu->parent_id) {
                                continue;
                            }
                            $menu->type = Menu::TYPE_PAGE;
                            $menu->language = $menu->parent->language;
                            $menu->link = $model->getUrl($menu->language);
                            $menu->label =  $model->getTranslation($menu->language)->title;
                            $menu->save();
                        }
                        continue;
                    }
                    $menu->link = $model->getUrl($menu->language);
                    if ($updateMenuItems && isset($updateMenuItems[$menu->id]) && $updateMenuItems[$menu->id]) {
                        $menu->label =  $model->getTranslation($menu->language)->title;
                    }
                    $menu->save();
                }


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
            'menus' => $menus,
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
        $menus = $model->menus;
        if (!$menus) {
            $menus = [new Menu()];
        }

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

                //Update menu items
                $updateMenuItems = Yii::$app->request->post('menu-items-update');
                foreach ($menus as $menu) {
                    if ($menu->isNewRecord) {
                        if (Yii::$app->request->post('menu-items-create')) {
                            $menu->page_id = $model->id;
                            $menu->parent_id = Yii::$app->request->post('menu-items-create-parent_id');
                            if (!$menu->parent_id) {
                                continue;
                            }
                            $menu->type = Menu::TYPE_PAGE;
                            $menu->language = $menu->parent->language;
                            $menu->link = $model->getUrl($menu->language);
                            $menu->label =  $model->getTranslation($menu->language)->title;
                            $menu->save();
                        }
                        continue;
                    }
                    $menu->link = $model->getUrl($menu->language);
                    if ($updateMenuItems && isset($updateMenuItems[$menu->id]) && $updateMenuItems[$menu->id]) {
                        $menu->label =  $model->getTranslation($menu->language)->title;
                    }
                    $menu->save();
                }

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
            'menus' => $menus,
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
