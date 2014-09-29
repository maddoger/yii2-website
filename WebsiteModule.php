<?php
/**
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name/
 * @copyright Copyright (c) 2013-2014 Vitaliy Syrchikov
 * @since 30.12.13
 */

namespace maddoger\website;

use Yii;
use maddoger\core\Module;
use yii\rbac\Item;

class WebsiteModule extends Module
{
	public $siteTitle = null;
	public $metaKeywords = null;
	public $metaDescription = null;
	public $layouts = null;
	public $defaultLayout = null;
	public $locales = null;

	public $headEndString = null;
	public $bodyBeginString = null;
	public $bodyEndString = null;

	protected $hasBackend = true;
	protected $hasFrontend = true;

	/**
	 * Translation category for Yii::t function
	 * @var string
	 */
	public $translationCategory = 'maddoger/website';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		//register translation messages from module
		//so no need do add to config/main.php
		Yii::$app->getI18n()->translations[$this->translationCategory] =
			array(
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => '@maddoger/website/messages',
			);
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return Yii::t('maddoger/website', '_module_name_');
	}

	/**
	 * @inheritdoc
	 */
	public function getDescription()
	{
		return Yii::t('maddoger/website', '_module_description_');
	}

	/**
	 * @inheritdoc
	 */
	public function getVersion()
	{
		return '0.1';
	}

	/**
	 * @inheritdoc
	 */
	public function getFaIcon()
	{
		return 'seo';
	}

	/**
	 * @inheritdoc
	 */
	public function isMultiLanguage()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getConfigurationModel()
	{
		$model = parent::getConfigurationModel();
		$model->addAttributes([
			'siteTitle' => ['label' => Yii::t('maddoger/website', 'Window title for website')],
			'metaKeywords' => ['label' => Yii::t('maddoger/website', 'Meta keywords for all website'), 'type' => 'textarea'],
			'metaDescription' => ['label' => Yii::t('maddoger/website', 'Meta description for all website'), 'type' => 'textarea'],

			'headEndString' => ['label' => Yii::t('maddoger/website', 'Code before </head> closing tag.'), 'type' => 'textarea'],
			'bodyBeginString' => ['label' => Yii::t('maddoger/website', 'Code right after <body> opening tag.'), 'type' => 'textarea'],
			'bodyEndString' => ['label' => Yii::t('maddoger/website', 'Code before </body> closing tag.'), 'type' => 'textarea',
				'hint' => Yii::t('maddoger/website', 'Use it for Google Analytics or Yandex Metrika counters.')
			],

			'layouts' => ['label' => Yii::t('maddoger/website', 'Available layouts for pages'),
				'hint' => Yii::t('maddoger/website', 'List separated by commas. Example: <code>default, narrow</code>. Also you can use labels: <code>default:Default layout, base:Base without container</code>.')],
			'defaultLayout' => ['label' => Yii::t('maddoger/website', 'Default layout')],
			'locales' => ['label' => Yii::t('maddoger/website', 'Available locales'),
				'hint' => Yii::t('maddoger/website', 'List separated by commas. Example: <code>ru, en</code>')],
		]);
		return $model;
	}

	/**
	 * @inheritdoc
	 */
	public function getDefaultRoutes()
	{
		return [
			[
				['*' => 'user/backend-auth/<action>'],
				Yii::t('maddoger/website', 'Backend authorization route'),
				Yii::t('maddoger/website', 'Provides authorization and password reset (with captcha) for backend application.')
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function getRbacRoles()
	{
		return [
			'page.create' => ['type'=>Item::TYPE_PERMISSION, 'description' => Yii::t('maddoger/website', 'Create new pages')],
			'page.read' => ['type'=>Item::TYPE_PERMISSION, 'description' => Yii::t('maddoger/website', 'View pages')],
			'page.update' => ['type'=>Item::TYPE_PERMISSION, 'description' => Yii::t('maddoger/website', 'Update pages')],
			'page.delete' => ['type'=>Item::TYPE_PERMISSION, 'description' => Yii::t('maddoger/website', 'Delete pages')],
			'page.manager' => [
				'type' => Item::TYPE_ROLE,
				'description' => Yii::t('maddoger/website', 'Pages manager'),
				'children' => [ 'page.create','page.read','page.update','page.delete' ]
			],

			'menu.manage' => ['type'=>Item::TYPE_PERMISSION, 'description' => Yii::t('maddoger/website', 'Manage menu')],
			'menu.manager' => [
				'type' => Item::TYPE_ROLE,
				'description' => Yii::t('maddoger/website', 'Menu manager'),
				'children' => [ 'menu.manage' ]
			],
		];
	}

	/**
	 * Returns navigation items for backend
	 * @return array
	 */
	public function getBackendNavigation()
	{
		return [
			[
				'label' => Yii::t('maddoger/website', 'Website'), 'fa' => 'book',
				'roles' => ['menu.manage', 'pages.read'],
				//'url' => 'user/user-backend/index',
				'items' => [
					/*['label' => Yii::t('maddoger/website', 'Structure'), 'fa'=>'book',
						'url'=> ['/website/structure/index'], 'activeUrl'=> ['/website/structure/*']],*/
					['label' => Yii::t('maddoger/website', 'Pages'), 'fa'=>'book', 'url'=> ['/website/backend/pages/index'],
						'activeUrl'=> ['/website/pages/*'], 'roles' => ['pages.read']],
					['label' => Yii::t('maddoger/website', 'Menu'), 'fa'=>'bars', 'url'=> ['/website/backend/menu/index'],
						'activeUrl'=> ['/website/menu/*'], 'roles' => ['menu.manage'],],
					['label' => Yii::t('maddoger/website', 'Settings'), 'fa'=>'gear',
						'url'=> ['/admin/backend/modules/config', 'module' => 'website', 'back_url' => Yii::$app->request->url],
						'roles' => ['admin.modulesConfiguration'],
					],
				],
			]
		];
	}

	public function getAvailableLayouts()
	{
		if ($this->layouts) {
			$res = [];
			foreach (explode(',', $this->layouts) as $layout) {
				$layout = trim($layout);
				if (empty($layout)) continue;
				$i = strpos($layout, ':');
				if ($i !== false) {
					$layoutKey = trim(substr($layout, 0, $i));
					$layoutTitle = trim(substr($layout, $i+1));
					$res[$layoutKey] = $layoutTitle;
				} else {
					$res[$layout] = $layout;
				}
			}
			return $res;
		} else {
			return [];
		}
	}

	public function getAvailableLocales()
	{
		if ($this->locales) {
			$res = [];
			foreach (explode(',', $this->locales) as $locale) {
				$locale = trim($locale);
				if (!empty($locale)) {
					$res[$locale] = $locale;
				}
			}
			return $res;
		} else {
			return [];
		}
	}
}