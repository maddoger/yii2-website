<?php
$this->title = $name.' #'.$code;
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="error-container">
				<h1 class="code"><?= $code ?></h1>
				<h2 class="name"><?= $name ?></h2>
				<div class="error-details">
					<?= $message ?>
				</div> <!-- /error-details -->
				<div class="error-actions">
					<a href="<?php Yii::$app->urlManager->createUrl('/..') ?>" class="btn btn-info btn-lg">
						<i class=fa fa-home"></i> <?= Yii::t('rusporting/website', 'Back to index') ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>