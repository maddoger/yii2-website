<div class="error-container">
	<h2 class="code"><?= $code ?></h1>
		<h3 class="name"><?= $name ?></h2>
	<div class="error-details">
		<?= $message ?>
	</div>
	<!-- /error-details -->
	<div class="error-actions">
		<a href="<?php Yii::$app->urlManager->createUrl('/..') ?>" class="btn btn-info">
			<i class=fa fa-home"></i> <?= Yii::t('rusporting/website', 'Back to index') ?>
		</a>
	</div>
</div>
