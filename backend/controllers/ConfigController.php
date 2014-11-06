<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend\controllers;
use maddoger\core\i18n\I18N;
use maddoger\website\common\models\Config;
use maddoger\website\frontend\Module;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * ConfigController.php
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package 
 */
class ConfigController extends Controller
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

    public function actionIndex()
    {
        $model = Config::getConfig(Module::className());

        if ($model->load(Yii::$app->request->post())) {

            //$validate = $model->validate();

            foreach (I18N::getAvailableLanguages() as $language) {
                $modelI18n = $model->getTranslation($language['locale']);
                $modelI18n->load(Yii::$app->request->post());
                //$validate = $validate && $modelI18n->validate();
            }

            if ($model->save()) {

                Yii::$app->session->addFlash('success', Yii::t('maddoger/website', 'Saved.'));

                return $this->refresh();
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}