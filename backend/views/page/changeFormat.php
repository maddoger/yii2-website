<?php

/* @var yii\web\View $this */
use maddoger\website\backend\Module;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var string $text */
/* @var string $fieldName */
/* @var array $formatInfo */

if (isset($formatInfo['widgetClass'])) {
    $widgetClass = $formatInfo['widgetClass'];
    $options = isset($formatInfo['widgetOptions']) ? $formatInfo['widgetOptions'] : [];
    $additionalOptions = Module::getInstance()->textEditorWidgetOptions;
    if ($additionalOptions) {
        $options = ArrayHelper::merge($options, $additionalOptions);
    }
    $options['name'] = $fieldName;
    $options['value'] = $text;
    echo $widgetClass::widget($options);
} else {
    echo Html::textarea($fieldName, $text, ['class' => 'form-control', 'rows' => 20]);
}