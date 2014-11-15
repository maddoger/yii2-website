<?php

/* @var yii\web\View $this */
use yii\helpers\Html;

/* @var string $text */
/* @var string $fieldName */
/* @var array $formatInfo */

if (isset($formatInfo['widgetClass'])) {
    $widgetClass = $formatInfo['widgetClass'];
    $options = isset($formatInfo['widgetOptions']) ? $formatInfo['widgetOptions'] : [];
    $options['name'] = $fieldName;
    $options['value'] = $text;
    echo $widgetClass::widget($options);
} else {
    echo Html::textarea($fieldName, $text, ['class' => 'form-control', 'rows' => 20]);
}