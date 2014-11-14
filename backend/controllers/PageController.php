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
use yii\helpers\Url;
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
                    [
                        'actions' => ['backup'],
                        'allow' => true,
                        'roles' => ['website.page.create', 'website.page.update'],
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
     * @param integer $original
     * @return mixed
     */
    public function actionCreate($original = 0)
    {
        $pageClass = Module::getInstance()->pageModelClass;
        /**
         * @var \maddoger\website\common\models\Page $model
         */
        $model = new $pageClass();
        if (!$original && ($time = $this->loadBackup($model))!==false) {
            Yii::$app->session->addFlash('warning', Yii::t('maddoger/website', 'Backup for {time, date} {time, time} is used! Click <a href="{url}">here</a> if you want to use original version.', [
                'url' => Url::to(['', 'original' => 1]),
                'time' => $time,
            ]));
        }

        $menus = [new Menu()];

        if ($this->saveModel($model, $menus)) {
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

        return $this->render('create', [
            'model' => $model,
            'menus' => $menus,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $original
     * @return mixed
     */
    public function actionUpdate($id, $original = 0)
    {
        $model = $this->findModel($id);
        if (!$original && ($time = $this->loadBackup($model))!==false) {
            Yii::$app->session->addFlash('warning', Yii::t('maddoger/website', 'Backup for {time, date} {time, time} is used! Click <a href="{url}">here</a> if you want to use original version.', [
                'url' => Url::to(['', 'id' => $id, 'original' => 1]),
                'time' => $time,
            ]));
        }

        $menus = $model->menus;
        if (!$menus) {
            $menus = [new Menu()];
        }

        if ($this->saveModel($model, $menus)) {
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

        return $this->render('update', [
            'model' => $model,
            'menus' => $menus,
        ]);
    }

    /**
     * @param Page $model
     * @param Menu[] $menus
     * @return bool
     */
    protected function saveModel($model, $menus)
    {
        if ($model->load(Yii::$app->request->post())) {

            $validate = true;
            foreach (I18N::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language['locale']);
                if ($modelI18n->load(Yii::$app->request->post())) {
                    if (empty($modelI18n->title) && empty($modelI18n->text)) {
                        if (!$modelI18n->isNewRecord) {
                            $modelI18n->delete();
                        }
                    } else {

                        //Meta data
                        if ($modelI18n->meta_data && isset($modelI18n->meta_data['name']) && isset($modelI18n->meta_data['value'])) {
                            $data = array_filter(array_combine(
                                $modelI18n->meta_data['name'],
                                $modelI18n->meta_data['value']
                            ));
                            if ($data) {
                                ksort($data);
                                $modelI18n->meta_data = $data;
                            } else {
                                $modelI18n->meta_data = null;
                            }
                        } else {
                            $modelI18n->meta_data = null;
                        }

                        if (!$modelI18n->validate()) {
                            $validate = false;
                        }
                    }
                }
            }

            if ($validate && $model->save()) {

                Yii::$app->session->remove('WEBSITE_PAGE_BACKUP_');
                Yii::$app->session->remove('WEBSITE_PAGE_BACKUP_'.$model->id);

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

                return true;
            }
        }
        return false;
    }

    /**
     * Backup data. AJAX only
     *
     * @param integer $id
     * @return mixed
     */
    public function actionBackup($id = null)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        $data = Yii::$app->request->post();
        $data['time'] = time();
        Yii::$app->session->set('WEBSITE_PAGE_BACKUP_'.$id, $data);
        return 'ok';
    }

    /**
     * Load model from backup
     * @param Page $model
     * @return bool if success
     */
    protected function loadBackup($model)
    {
        $data = Yii::$app->session->get('WEBSITE_PAGE_BACKUP_'.$model->id);
        if ($data && $model->load($data)) {
            $isDirty = count($model->getDirtyAttributes(['slug']))>0;
            foreach (I18N::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language['locale']);
                if ($modelI18n->load($data)) {
                    //Meta data
                    if ($modelI18n->meta_data && isset($modelI18n->meta_data['name']) && isset($modelI18n->meta_data['value'])) {
                        $metaData = array_filter(array_combine(
                            $modelI18n->meta_data['name'],
                            $modelI18n->meta_data['value']
                        ));
                        if ($metaData) {
                            ksort($metaData);
                            $modelI18n->meta_data = $metaData;
                        } else {
                            $modelI18n->meta_data = null;
                        }
                    } else {
                        $modelI18n->meta_data = null;
                    }
                    $isDirty = $isDirty ||
                        (count($modelI18n->getDirtyAttributes())>0);
                }
            }
            if ($isDirty) {
                if (isset($data['time'])) {
                    return $data['time'];
                } else {
                    return 0;
                }
            }
        }
        return false;
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
