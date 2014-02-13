<?php

namespace maddoger\website\modules\backend\controllers;

use maddoger\website\models\Menu;
use maddoger\core\BackendController;
use yii\db\Exception;
use yii\web\VerbFilter;
use Yii;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends BackendController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => 'yii\web\AccessControl',
				'rules' => [
					[
						'allow' => true,
						'roles' => ['menu.manage'],
					],
					[
						'allow' => false,
					]
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
	 *
	 * @return mixed
	 */
	public function actionIndex()
	{
		$this->title = Yii::t('maddoger/website', 'Menu');
		$items = Menu::getTreeByParentId();

		if (Yii::$app->request->isPost && isset($_POST['level'])) {

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
						$model = Menu::find($array['id']);
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
		}

		return $this->render('index', [
			'items' => $items,
		]);
	}
}
