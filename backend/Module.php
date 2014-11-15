<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\website\backend;

use maddoger\core\BackendModule;
use maddoger\website\common\models\Config;
use maddoger\website\common\models\PageI18n;
use Yii;
use yii\helpers\Markdown;
use yii\helpers\Url;
use yii\rbac\Item;

/**
 * Website Module
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package yii2-website
 */
class Module extends BackendModule
{
    /**
     * @var string page model class
     */
    public $pageModelClass = 'maddoger\website\common\models\Page';

    /**
     * @var array information about text formats
     * Each item is format. Key is format id, value is format description.
     * Description fields:
     * `label` - format label;
     * `formatter` - source to html Closure `string function($model)`. If is null, copy will be used;
     * `widgetClass` - widget class for editor
     * `widgetOptions` - widget options
     */
    public $textFormats;

    /**
     * @var \maddoger\website\common\models\Config Module configuration
     */
    public $config;

    /**
     * Init module
     */
    public function init()
    {
        parent::init();
        $this->config = Config::getConfig('\maddoger\website\frontend\Module',
            is_array($this->config) ? $this->config : []
        );

        if (!isset(Yii::$app->i18n->translations['maddoger/website'])) {

            Yii::$app->i18n->translations['maddoger/website'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@maddoger/website/common/messages',
                'sourceLanguage' => 'en-US',
            ];
        }

        if (!$this->textFormats && isset(Yii::$app->params['textFormats'])) {
            $this->textFormats = Yii::$app->params['textFormats'];
        }
        if (!$this->textFormats) {
            $this->textFormats = [
                'text' => [
                    'label' => Yii::t('maddoger/website', 'Text'),
                    //no widget, simple textarea
                    'formatter' => function ($text) {
                        return Yii::$app->formatter->asNtext($text);
                    }
                ],
                'md' => [
                    'label' => Yii::t('maddoger/website', 'Markdown'),
                    //no widget, simple textarea
                    'formatter' => function ($text) {
                        return Markdown::process($text, 'gfm');
                    }
                ],
                'html' => [
                    'label' => Yii::t('maddoger/website', 'HTML'),
                ],
                'raw' => [
                    'label' => Yii::t('maddoger/website', 'Raw'),
                ],
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Yii::t('maddoger/website', 'Website Module');
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * @inheritdoc
     */
    public function getNavigation()
    {
        return [
            [
                'label' => Yii::t('maddoger/website', 'Website'),
                'icon' => 'fa fa-book',
                'items' => [
                    [
                        'label' => Yii::t('maddoger/website', 'Pages'),
                        'url' => ['/' . $this->id . '/page/index'],
                        'activeUrl' => '/' . $this->id . '/page/*',
                        'icon' => 'fa fa-book',
                        'roles' => ['website.page.view'],
                    ],
                    [
                        'label' => Yii::t('maddoger/website', 'Menus'),
                        'url' => ['/' . $this->id . '/menu/index'],
                        'activeUrl' => '/' . $this->id . '/menu/*',
                        'icon' => 'fa fa-bars',
                        'roles' => ['website.menu.manage'],
                    ],
                    [
                        'label' => Yii::t('maddoger/website', 'Configuration'),
                        'url' => ['/' . $this->id . '/config'],
                        'activeUrl' => '/' . $this->id . '/config/*',
                        'icon' => 'fa fa-gears',
                        'roles' => ['website.config.manage'],
                    ],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRbacItems()
    {
        return [
            //Page
            'website.page.viewHiddenPages' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. View hidden pages'),
                ],
            'website.page.view' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. View pages'),
                ],
            'website.page.create' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. Create pages'),
                ],
            'website.page.update' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. Update pages'),
                ],
            'website.page.delete' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. Delete pages'),
                ],
            'website.page.manager' =>
                [
                    'type' => Item::TYPE_ROLE,
                    'description' => Yii::t('maddoger/website', 'Website. Manage pages'),
                    'children' => [
                        'website.page.viewHiddenPages',
                        'website.page.view',
                        'website.page.create',
                        'website.page.update',
                        'website.page.delete',
                    ],
                ],
            //Menu
            'website.menu.manage' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. Create, update and delete menus'),
                ],
            'website.menu.manager' =>
                [
                    'type' => Item::TYPE_ROLE,
                    'description' => Yii::t('maddoger/website', 'Website. Manage menus'),
                    'children' => [
                        'website.menu.manage',
                    ]
                ],
            //Website
            'website.config.manage' =>
                [
                    'type' => Item::TYPE_PERMISSION,
                    'description' => Yii::t('maddoger/website', 'Website. Change SEO settings'),
                ],
            'website.config.manager' =>
                [
                    'type' => Item::TYPE_ROLE,
                    'description' => Yii::t('maddoger/website', 'Website. Change SEO settings'),
                    'children' => [
                        'website.config.manage',
                    ]
                ],
        ];
    }

    /**
     * @return array
     */
    public function getSearchSources()
    {
        return [
            [
                'class' => '\maddoger\core\search\ArraySearchSource',
                'data' => [
                    [
                        'label' => Yii::t('maddoger/website', 'Pages'),
                        'url' => ['/' . $this->id . '/page/index'],
                    ],
                    [
                        'label' => Yii::t('maddoger/website', 'Create new page'),
                        'url' => ['/' . $this->id . '/page/create'],
                    ],
                    [
                        'label' => Yii::t('maddoger/website', 'Menus'),
                        'url' => ['/' . $this->id . '/menu/index'],
                    ],
                    [
                        'label' => Yii::t('maddoger/website', 'SEO Configuration'),
                        'url' => ['/' . $this->id . '/config'],
                    ],
                ],
                'roles' => ['website.page.view', 'website.config.manage', 'website.menu.manage'],
            ],
            [
                'class' => '\maddoger\core\search\ActiveSearchSource',
                'modelClass' => '\maddoger\website\common\models\Menu',
                'searchAttributes' => ['label'],
                'url' => ['/' . $this->id . '/menu/index'],
                'labelPrefix' => Yii::t('maddoger/website', 'Menu - '),
                'roles' => ['website.menu.manage'],
            ],
            [
                'class' => '\maddoger\core\search\ActiveSearchSource',
                'query' => PageI18n::find(),
                'searchAttributes' => ['title'],
                'url' => function($model){
                    return Url::to(['/' . $this->id . '/page/view', 'id' => $model['page_id']]);
                },
                'label' => 'title',
                'labelPrefix' => Yii::t('maddoger/website', 'Page - '),
                'roles' => ['website.page.view'],
            ],
        ];
    }
}