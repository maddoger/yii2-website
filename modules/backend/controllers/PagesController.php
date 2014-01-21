<?php

namespace rusporting\website\modules\backend\controllers;

use rusporting\website\models\Page;
use rusporting\website\modules\backend\models\PageSearch;
use rusporting\core\BackendController;
use yii\base\InvalidCallException;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\validators\FileValidator;
use yii\validators\ImageValidator;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\VerbFilter;
use Yii;

/**
 * PagesController implements the CRUD actions for Page model.
 */
class PagesController extends BackendController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => 'yii\web\AccessControl',
				'rules' => [
					[
						'actions' => ['index', 'view'],
						'allow' => true,
						'roles' => ['page.read'],
					],
					[
						'actions' => ['create', 'copy', 'file-upload', 'image-upload', 'clipboard-upload'],
						'allow' => true,
						'roles' => ['page.create'],
					],
					[
						'actions' => ['update', 'file-upload', 'image-upload', 'clipboard-upload'],
						'allow' => true,
						'roles' => ['page.update'],
					],
					[
						'actions' => ['delete'],
						'allow' => true,
						'roles' => ['page.delete'],
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
	 * Lists all Page models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new PageSearch;
		$dataProvider = $searchModel->search($_GET);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
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
		$model = new Page;

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Creates a new Page model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCopy($id)
	{
		$model = new Page;
		$copyModel = $this->findModel($id);
		$model->setAttributes($copyModel->getAttributes(null, ['id']), false);

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('create', [
				'model' => $model,
			]);
		}
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

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
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

	public function actionFileUpload()
	{
		$validator = new FileValidator();
		$error = null;

		$file = UploadedFile::getInstanceByName('file');

		if($validator->validate($file, $error))
		{
			$path = '/uploads/pages/'.date('Y/m/d');
			$fileName =
				Inflector::slug(pathinfo($file->name, PATHINFO_FILENAME)).'_'.
				time().'.'.strtolower(pathinfo($file->name, PATHINFO_EXTENSION));

			$base = Yii::getAlias('@frontendUrl'.$path);

			$fileDir = Yii::getAlias('@frontendPath'.$path);
			if (!is_dir($fileDir)) {
				FileHelper::createDirectory($fileDir);
			}

			if($file->saveAs($fileDir.'/'.$fileName))
			{
				$array = array(
					'filelink' => $base.'/'.$fileName,
					'filename' => $file->name
				);
				return json_encode($array);
			}
		}

		//Yii::$app->response->setStatusCode('400');
		return json_encode(['error'=>\Yii::t('rusporting/website', 'Bad file.')]);
	}

	public function actionImageUpload()
	{
		$image = new ImageValidator();
		$error = null;

		$file = UploadedFile::getInstanceByName('file');

		if($image->validate($file, $error))
		{
			$path = '/uploads/pages/'.date('Y/m/d');
			$fileName =
				Inflector::slug(pathinfo($file->name, PATHINFO_FILENAME)).'_'.
				time().'.'.strtolower(pathinfo($file->name, PATHINFO_EXTENSION));

			$base = Yii::getAlias('@frontendUrl'.$path);

			$fileDir = Yii::getAlias('@frontendPath'.$path);
			if (!is_dir($fileDir)) {
				FileHelper::createDirectory($fileDir);
			}

			if($file->saveAs($fileDir.'/'.$fileName))
			{
				$array = array(
					'filelink' => $base.'/'.$fileName,
				);
				return json_encode($array);
			}
		}

		//Yii::$app->response->setStatusCode('400');
		return json_encode(['error'=>\Yii::t('rusporting/website', 'Bad file.')]);
	}

	public function actionClipboardUpload()
	{
		if (!isset($_POST['contentType']) || !isset($_POST['data'])) {
			throw new BadRequestHttpException();
		}

		$contentType = $_POST['contentType'];
		$data = base64_decode($_POST['data']);
		$fileName = md5($data).'.png';

		$path = '/uploads/pages/'.date('Y/m/d');
		$base = Yii::getAlias('@frontendUrl'.$path);

		$fileDir = Yii::getAlias('@frontendPath'.$path);
		if (!is_dir($fileDir)) {
			FileHelper::createDirectory($fileDir);
		}

		if(file_put_contents($fileDir.'/'.$fileName, $data))
		{
			$array = array(
				'filelink' => $base.'/'.$fileName,
			);
			return json_encode($array);
		}

		//Yii::$app->response->setStatusCode('400');
		return json_encode(['error'=>\Yii::t('rusporting/website', 'Bad file.')]);
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
		if (($model = Page::find($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException(\Yii::t('rusporting/website', 'The requested page does not exist.'));
		}
	}
}
