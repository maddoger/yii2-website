<?php
/**
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name/
 * @copyright Copyright (c) 2013-2014 Rusporting Inc.
 * @since 30.12.13
 */

namespace rusporting\website;

use Yii;
use rusporting\core\Module;
use yii\rbac\Item;

class WebsiteModule extends Module
{
	public $siteTitle = null;
	public $metaKeywords = null;
	public $metaDescription = null;
	public $layouts = null;
	public $defaultLayout = null;
	public $locales = null;

	protected $hasBackend = true;
	protected $hasFrontend = true;

	/**
	 * Translation category for Yii::t function
	 * @var string
	 */
	public $translationCategory = 'rusporting/website';

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
				'basePath' => '@rusporting/website/messages',
			);
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return Yii::t('rusporting/website', '_module_name_');
	}

	/**
	 * @inheritdoc
	 */
	public function getDescription()
	{
		return Yii::t('rusporting/website', '_module_description_');
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
			'siteTitle' => ['label' => Yii::t('rusporting/website', 'Window title for website')],
			'metaKeywords' => ['label' => Yii::t('rusporting/website', 'Meta keywords for all website'), 'type' => 'textarea'],
			'metaDescription' => ['label' => Yii::t('rusporting/website', 'Meta description for all website'), 'type' => 'textarea'],
			'layouts' => ['label' => Yii::t('rusporting/website', 'Available layouts for pages'),
				'hint' => Yii::t('rusporting/website', 'List separated by commas. Example: <code>default, narrow</code>. Also you can use labels: <code>default:Default layout, base:Base without container</code>.')],
			'defaultLayout' => ['label' => Yii::t('rusporting/website', 'Default layout')],
			'locales' => ['label' => Yii::t('rusporting/website', 'Available locales'),
				'hint' => Yii::t('rusporting/website', 'List separated by commas. Example: <code>ru, en</code>')],
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
				Yii::t('rusporting/website', 'Backend authorization route'),
				Yii::t('rusporting/website', 'Provides authorization and password reset (with captcha) for backend application.')
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function getRbacRoles()
	{
		return [
			'page.create' => ['type'=>Item::TYPE_OPERATION, 'description' => Yii::t('rusporting/website', 'Create new pages')],
			'page.read' => ['type'=>Item::TYPE_OPERATION, 'description' => Yii::t('rusporting/website', 'View pages')],
			'page.update' => ['type'=>Item::TYPE_OPERATION, 'description' => Yii::t('rusporting/website', 'Update pages')],
			'page.delete' => ['type'=>Item::TYPE_OPERATION, 'description' => Yii::t('rusporting/website', 'Delete pages')],
			'page.manager' => [
				'type' => Item::TYPE_ROLE,
				'description' => Yii::t('rusporting/website', 'Pages manager'),
				'children' => [ 'page.create','page.read','page.update','page.delete' ]
			],

			'menu.manage' => ['type'=>Item::TYPE_OPERATION, 'description' => Yii::t('rusporting/website', 'Manage menu')],
			'menu.manager' => [
				'type' => Item::TYPE_ROLE,
				'description' => Yii::t('rusporting/website', 'Menu manager'),
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
				'label' => Yii::t('rusporting/website', 'Website'), 'fa' => 'book',
				//'url' => 'user/user-backend/index',
				'items' => [
					/*['label' => Yii::t('rusporting/website', 'Structure'), 'fa'=>'book',
						'url'=> ['/website/structure/index'], 'activeUrl'=> ['/website/structure/*']],*/
					['label' => Yii::t('rusporting/website', 'Pages'), 'fa'=>'book', 'url'=> ['/website/pages/index'],
						'activeUrl'=> ['/website/pages/*']],
					['label' => Yii::t('rusporting/website', 'Menu'), 'fa'=>'bars', 'url'=> ['/website/menu/index'],
						'activeUrl'=> ['/website/menu/*']],
					['label' => Yii::t('rusporting/website', 'Settings'), 'fa'=>'gear',
						'url'=> ['/admin/modules/config?module=website&back_url='.urlencode(Yii::$app->request->url)],
						'activeUrl'=> ['/admin/modules/config?module=website']],
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
					$layoutKey = substr($layout, 0, $i);
					$layoutTitle = substr($layout, $i+1);
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