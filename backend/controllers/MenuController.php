<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend\controllers;

use maddoger\website\common\models\Menu;
use maddoger\website\common\models\PageI18n;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MenuController
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-website
 */

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['website.menu.manage'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Menu tree
     */
    public function actionIndex($id = null)
    {
        $menus = Menu::findMenus();
        /**
         * @var Menu $menu
         */
        $menu = null;

        if ($id === null) {
            if (count($menus) > 0) {
                reset($menus);
                $id = key($menus);
                $menu = $menus[$id];
            }
        } elseif ($id) {
            if (isset($menus[$id])) {
                $menu = $menus[$id];
            } else {
                throw new NotFoundHttpException(Yii::t('maddoger/website', 'Menu not found.'));
            }
        }

        //New menu
        if (!$menu) {
            $menu = new Menu();
            $menu->type = Menu::TYPE_MENU;
            $menu->status = Menu::STATUS_DRAFT;
        }

        //New item
        $newItem = new Menu();
        $newItem->scenario = 'newItem';
        $newItem->status = Menu::STATUS_DRAFT;

        if (Yii::$app->request->isPost) {

            if (!$menu->isNewRecord) {

                //New item
                if ($newItem->load(Yii::$app->request->post())) {
                    $newItem->parent_id = $menu->id;
                    $newItem->language = $menu->language;

                    if ($newItem->save()) {
                        if (Yii::$app->request->isAjax) {
                            return $this->renderPartial('_item', ['item' => $newItem]);
                        } else {
                            return $this->refresh();
                        }
                    }
                }

                //Items
                $itemsSort = @array_flip(Yii::$app->request->post('items_sort'));
                $itemsDelete = Yii::$app->request->post('items_delete');
                $items = Yii::$app->request->post('menu-items');
                if ($items) {
                    foreach ($items as $id => $itemArray) {
                        $item = Menu::findOne($id);
                        if (!$item) {
                            continue;
                        }
                        if (isset($itemsDelete[$id]) && $itemsDelete[$id]) {
                            $item->delete();
                            continue;
                        }
                        $item->scenario = 'updateMenuItems';
                        $item->setAttributes($itemArray);
                        $item->language = $menu->language;
                        $item->sort = @$itemsSort[$item->id] + 1;
                        $item->status = Menu::STATUS_ACTIVE;
                        $item->save();
                    }
                }
            }

            if ($menu->load(Yii::$app->request->post()) && $menu->save()) {
                Yii::$app->session->addFlash('success', Yii::t('maddoger/website', 'Menu successfully saved.'));
                return $this->redirect(['', 'id' => $menu->id]);
            }
        }

        return $this->render('index', [
            'menus' => $menus,
            'menu' => $menu,
            'newItem' => $newItem,
        ]);
    }

    public function actionPages($q, $sort = 'label')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sortAttributes = ['title' => SORT_ASC, 'updated_at' => SORT_DESC];
        if (!$sort || !isset($sortAttributes[$sort])) {
            throw new InvalidParamException('Unknown sort field.');
        }

        $query = PageI18n::find()->where([
                'like',
                'title',
                $q
            ])->orderBy([$sort => $sortAttributes[$sort]])->with('page');
        $query->limit(20);
        $res = [];
        foreach ($query->all() as $model) {
            /**
             * @var PageI18n $model
             */
            $res[] = [
                'id' => $model->page_id,
                'text' => $model->title,
                'title' => $model->title,
                'url' => $model->page->getUrl($model->language),
            ];
        }
        return $res;
    }

    public function actionDelete($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            $model->delete();
        } else {
            throw new NotFoundHttpException(Yii::t('maddoger/website', 'The requested menu does not exist.'));
        }
    }
}
