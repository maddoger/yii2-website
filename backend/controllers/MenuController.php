<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend\controllers;

use maddoger\website\backend\models\MenuForm;
use maddoger\website\backend\models\MenuItemForm;
use maddoger\website\backend\models\MenuNewItemForm;
use maddoger\website\common\models\Menu;
use Yii;
use yii\base\Exception;
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
        $newItem = new MenuNewItemForm();
        $newItem->status = Menu::STATUS_DRAFT;

        $items = MenuItemForm::getTreeByParentId($menu->id);

        if (Yii::$app->request->isPost) {

            if (!$menu->isNewRecord) {
                if ($newItem->load(Yii::$app->request->post())) {
                    $newItem->parent_id = $menu->id;
                    if ($newItem->save()) {
                        if (Yii::$app->request->isAjax) {
                            return $this->redirect(['', 'id' => $menu->id]);
                        } else {
                            return $this->refresh();
                        }
                    }
                }
            }

            if ($menu->load(Yii::$app->request->post()) && $menu->save()) {
                Yii::$app->session->addFlash('success', Yii::t('maddoger/website', 'Menu successfully saved.'));
                return $this->redirect(['', 'id' => $menu->id]);
            }
        }

        /*if (Yii::$app->request->isPost && isset($_POST['level'])) {

            $c = count($_POST['level']);
            //Массив, куда будут записаны элементы
            $id_by_level = array(1 => 0);
            for ($i = 0; $i < $c; $i++) {
                //Получаем данные
                $level = $_POST['level'][$i];

                $array = array(
                    'id' => $_POST['id'][$i],
                    'link' => $_POST['link'][$i],
                    'preg' => $_POST['preg'][$i],
                    'title' => $_POST['title'][$i],
                    'css_class' => $_POST['css_class'][$i],
                    'element_id' => $_POST['element_id'][$i],
                    'enabled' => $_POST['enabled'][$i],
                    'parent_id' => $id_by_level[$level],
                    'sort' => $i
                );
                if ($array['parent_id'] == 0) {
                    $array['parent_id'] = null;
                }

                //Если элемент отмечен на удаление
                if ($_POST['delete'][$i]) {
                    //И id задан, то удаляем
                    if ($array['id']) {
                        Menu::deleteAll(['id' => $array['id']]);
                    }
                } else {
                    if (!$array['id']) {
                        unset($array['id']);
                        $model = new Menu();
                        $model->setAttributes($array, false);
                        if (!$model->save()) {
                            throw new Exception('Saving error');
                        }
                        $array['id'] = $model->id;
                    } else {
                        //Заменяем
                        $model = Menu::findOne($array['id']);
                        $model->setAttributes($array, false);
                        if (!$model->save()) {
                            throw new Exception('Saving error');
                        }
                    }
                }
                //Записываем указатель на следующий уровень
                $id_by_level[$level + 1] = $array['id'];
            }
            return $this->redirect(['index']);
        }*/

        return $this->render('index', [
            'items' => $items,
            'menus' => $menus,
            'menu' => $menu,
            'newItem' => $newItem,
        ]);
    }
}
