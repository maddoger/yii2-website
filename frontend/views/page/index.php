<?php

/* @var $this yii\web\View */
/* @var $model \maddoger\website\common\models\Page */

$this->title = $model->window_title ?: $model->title;
$this->registerMetaTag(['name' => 'keywords', 'value' => $model->meta_keywords]);
$this->registerMetaTag(['name' => 'description', 'value' => $model->meta_description]);

echo '<div class="website-'.preg_replace('/[^\w-]/', '', $model->slug).'">';
echo '<h2>' . $model->title . '</h2>';
echo $model->text;
echo '</div>';