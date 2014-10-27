<div class="error-container">
    <h2 class="code"><?= $code ?></h2>

    <h3 class="name"><?= $name ?></h3>

    <div class="error-details">
        <?= $message ?>
    </div>
    <!-- /error-details -->
    <div class="error-actions">
        <a href="<?= Yii::$app->request->getReferrer(); ?>" class="btn">
            <i class="fa fa-arrow-left"></i> <?= Yii::t('maddoger/website', 'Return back') ?>
        </a> &nbsp;
        <a href="<?php echo Yii::$app->urlManager->createUrl('/') ?>" class="btn">
            <i class=fa fa-home"></i> <?= Yii::t('maddoger/website', 'Back to index') ?>
        </a>
    </div>
</div>
