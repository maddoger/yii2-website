<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var rusporting\website\models\Page $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="page-form">

	<?php $form = ActiveForm::begin(
		[
			'options' => array('class' => 'form-horizontal'),
			'fieldConfig' => array(
				'labelOptions' => ['class' => 'control-label col-lg-2'],
				'template' => "{label}\n<div class=\"col-lg-10\">{input}\n{error}\n{hint}</div>",
				'hintOptions' => ['class' => 'hint-block text-muted small'],
			),
		]
	); ?>

		<?= $form->field($model, 'title')->textInput(['maxlength' => 150])->hint(Yii::t('rusporting/website', 'Page title.')) ?>

		<?= $form->field($model, 'window_title')->textInput(['maxlength' => 150])->hint(Yii::t('rusporting/website', 'Window title. May be longer than page title.')) ?>

		<?= $form->field($model, 'slug')->textInput(['maxlength' => 150])->
			hint(Yii::t('rusporting/website', 'URL where page will be published. Example: <code>/index</code> will be <code>{domain}/index</code>.', ['domain' => Yii::$app->request->hostInfo])); ?>

		<?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

		<?= $form->field($model, 'published')->textInput() ?>


		<?= $form->field($model, 'locale')->textInput(['maxlength' => 10]) ?>

		<?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'meta_description')->textInput(['maxlength' => 255]) ?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? \Yii::t('rusporting/website', 'Create') : \Yii::t('rusporting/website', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
