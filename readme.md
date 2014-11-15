Yii2 Website manager by maddoger

## Installation

1) clone
2) migrate: ##yii migrate --migrationPath=@maddoger/website/common/migrations##
3) modules:

'modules' => [
		...
		'website' => 'maddoger\website\frontend\WebsiteModule',
		'website-backend' => 'maddoger\website\backend\WebsiteModule',
		...
	],

## Text formats

```php
Yii->$app->params['textFormats'] => 
  [
      'text' => [
          'label' => 'Text',
          //no widget, simple textarea
          'formatter' => function ($text) {
              return Yii::$app->formatter->asNtext($text);
          }
      ],
      'md' => [
          'label' => 'Markdown',
          //no widget, simple textarea
          'formatter' => function ($text) {
              return yii\helpers\Markdown::process($text, 'gfm');
          }
      ],
      'html' => [
          'label' => Yii::t('maddoger/website', 'HTML'),
          'widgetClass' => '\vova07\imperavi\Widget',
      ],
      'raw' => [
          'label' => Yii::t('maddoger/website', 'Raw'),
      ],
  ],
```

## URL rule

```php
'<languageSlug:[\w-]+>/<slug:.*?>' => 'website/page/index',
```