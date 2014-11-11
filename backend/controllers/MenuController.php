<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend\controllers;

use maddoger\website\backend\models\MenuForm;
use maddoger\website\backend\models\MenuItemForm;
use maddoger\website\backend\models\MenuNewItemForm;
use maddoger\website\backend\models\PageSearch;
use maddoger\website\common\models\Menu;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
                        'roles' => ['website.menu.manageMenu'],
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
    public function actionIndex($id=null)
    {
        $menus = MenuForm::findMenus();
        $menu = null;

        if ($id === null) {
            if (count($menus)>0) {
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

                    if ($newItem->type == Menu::TYPE_PAGE) {
                        $page = $newItem->page;
                        if ($page) {
                            if ($menu->language) {
                                $page->setLanguage($menu->language);
                            }
                            $newItem->title = $page->title;
                            $newItem->link = $page->getUrl();
                        }
                    }

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
                $items = Yii::$app->request->post('menu-items');
                if ($items) {
                    foreach ($items as $id=>$itemArray) {
                        $item = Menu::findOne($id);
                        $item->scenario = 'updateMenuItems';
                        if (!$item) {
                            continue;
                        }
                        $item->setAttributes($itemArray);
                        $item->sort = @$itemsSort[$item->id]+1;
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

        $items = MenuItemForm::getTreeByParentId($menu->id, false);

        return $this->render('index', [
            'items' => $items,
            'menus' => $menus,
            'menu' => $menu,
            'newItem' => $newItem,
        ]);
    }

    public function actionPages($q)
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(['title' => $q]);
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
