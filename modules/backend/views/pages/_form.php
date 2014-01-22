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

		<p>
			<?= Html::submitButton($model->isNewRecord ? \Yii::t('rusporting/website', 'Create') : \Yii::t('rusporting/website', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</p>

		<ul class="nav nav-tabs">
			<li class="active"><a href="#common" data-toggle="tab"><?= Yii::t('rusporting/website', 'Common') ?></a></li>
			<li><a href="#additional" data-toggle="tab"><?= Yii::t('rusporting/website', 'Additional') ?></a></li>
		</ul>
		<br />
		<div class="tab-content">

			<div class="tab-pane active" id="common">
				<?= $form->field($model, 'title')->textInput(['id'=>'page-caption', 'maxlength' => 150])->hint(Yii::t
					('rusporting/website',
																													  'Page title.')) ?>

				<?= $form->field($model, 'window_title')->textInput(['id'=>'page-title', 'maxlength' => 150])->hint(Yii::t('rusporting/website', 'Window title. May be longer than page title.')) ?>

				<?= $form->field($model, 'slug')->textInput(['id'=>'page-url', 'maxlength' => 150])->
					hint(Yii::t('rusporting/website', 'URL where page will be published. Example: <code>/index</code> will be <code>{domain}/index</code>.', ['domain' => Yii::$app->request->hostInfo.Yii::getAlias('@frontendUrl')])); ?>

				<?php

				/*$options = [
					// You can either use it for model attribute
					'model' => $model,
					'attribute' => 'text',
					// Some options, see http://imperavi.com/redactor/docs/
					'options' => [
						'lang' => Yii::$app->language,
						'convertDivs' => false,
						'minHeight' => '100',
						'imageUpload' => Yii::$app->urlManager->createUrl('/admin/files/image-upload'),
						'clipboardUploadUrl' => Yii::$app->urlManager->createUrl('admin/files/clipboard-upload'),
						'fileUpload' => Yii::$app->urlManager->createUrl('/admin/files/file-upload'),
						'imageUploadErrorCallback' => 'function(json) { alert(json.error); }',

						// if you are using CSRF protection – add following:
						'uploadFields'=>array(
							Yii::$app->request->csrfVar => Yii::$app->request->getCsrfToken(),
							//Internal folder for file uploading
							'folder' => empty($model->slug) ? '/page/'.date('Y/m/d') : '/page'.$model->slug
						),
					]
				];*/

				//echo $form->field($model, 'text')->widget('rusporting\redactor\Widget', $options);
				echo $form->field($model, 'text')->widget('rusporting\admin\widgets\TextEditor', [
					'model' => $model,
					'attribute' => 'text',
					'config' => [
						'rows' => 100,
					]
				]);
				//echo $form->field($model, 'text')->textarea(['rows' => 6]);
				?>
			</div>

			<div class="tab-pane" id="additional">
				<?= $form->field($model, 'published')->dropDownList(\rusporting\website\models\Page::publishListValues()) ?>

				<?= $form->field($model, 'meta_keywords')->textarea()->hint(Yii::t('rusporting/website', 'Keywords separated by commas. Example: <code>bread, cookies</code>.')) ?>

				<?= $form->field($model, 'meta_description')->textarea()->hint(Yii::t('rusporting/website', 'Short description of page content.')) ?>

				<?= $form->field($model, 'locale')->dropDownList(Yii::$app->getModule('website')->getAvailableLocales(),
					['prompt' => Yii::t('rusporting/website', 'All')])->hint(Yii::t('rusporting/website', 'Language of page.')) ?>

				<?= $form->field($model, 'layout')->dropDownList(Yii::$app->getModule('website')->getAvailableLayouts(),
					['prompt' => Yii::t('rusporting/website', 'Default')])->hint(Yii::t('rusporting/website', 'Layout using for page output.')) ?>

			</div>

		</div>

		<p>
			<?= Html::submitButton($model->isNewRecord ? \Yii::t('rusporting/website', 'Create') : \Yii::t('rusporting/website', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</p>

	<?php ActiveForm::end(); ?>

</div>
<?php

$this->registerJs(
	 <<<HERE
		/* Транслит */

	var translitArray = {
		'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ё':'yo', 'ж':'zh', 'з':'z', 'и':'i', 'й':'y', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o', 'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'х':'h', 'ч':'ch', 'ц':'c', 'ш':'sh', 'щ':'sch', 'ъ':'', 'ы':'y', 'ь':'', 'э':'e', 'ю':'yu', 'я':'ya',
		' ':'-', '-':'-',
		'a':'a', 'b':'b', 'c':'c', 'd':'d', 'e':'e', 'f':'f', 'g':'g', 'h':'h', 'i':'i', 'j':'j', 'k':'k', 'l':'l', 'm':'m', 'n':'n', 'o':'o', 'p':'p', 'q':'q', 'r':'r', 's':'s', 't':'t', 'u':'u', 'v':'v', 'w':'w', 'x':'x', 'y':'y', 'z':'z',
		'1':'1', '2':'2', '3':'3', '4':'4', '5':'5', '6':'6', '7':'7', '8':'8', '9':'9', '0':'0' };

	function toTranslit(str) {
		//В нижний регистр
		str = str.toLowerCase();
		var res = '';
		var len = str.length;

		for (var i = 0; i < len; i++) {
			if (translitArray[str[i]] != undefined) {
				if ((translitArray[str[i]] == '-') && (res[res.length - 1] == '-'))
					continue;
				res += translitArray[str[i]];
			}
		}
		if (res[res.length - 1] == '-')
			res = res.substr(0, res.length - 1);
		return res;
	}

	$(document).ready(
    function () {
        $('#page-title').on('keydown',
            function (event) {
				$(this).data('sync', false);
			}
        );
        $('#page-url').on('keydown',
			function (event) {
				$(this).data('sync', false);
			}
		);


        $('#page-caption').on('keydown',
			function (event) {
				var title = $('#page-title');
				if (( $(title).val() == '' ) || ( $(title).val() == $(this).val() )) {
					$(title).data('sync', true);
				}

				var url = $('#page-url');
				var value = '/' + toTranslit($(this).val()) + '.html';

				if (($(url).val() == '') || ($(url).val() == value)) {
					$(url).data('sync', true);
				}

			}
		).on('keyup',
			function (event) {
				var title = $('#page-title');

				if ($(title).data('sync')) {
					$(title).val($(this).val());
				}

				var url = $('#page-url');

				if ($(url).data('sync')) {
					$(url).val('/' + toTranslit($(this).val()));
				}
			}
		);
    }
);
HERE
);

?>