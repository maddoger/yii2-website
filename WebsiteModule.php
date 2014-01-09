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
		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function getDefaultRoutes()
	{
		return [
			[
				['user/<action:(login|logout|captcha|request-password-reset|reset-password)>' => 'user/backend-auth/<action>'],
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
				'label' => Yii::t('rusporting/website', 'Website'), 'fa' => 'books',
				//'url' => 'user/user-backend/index',
				'items' => [
					['label' => Yii::t('rusporting/website', 'Pages'), 'fa'=>'book', 'url'=> ['/website/pages/index'], 'activeUrl'=> ['/website/pages/*']],
					['label' => Yii::t('rusporting/website', 'Menu'), 'fa'=>'bars', 'url'=> ['/website/menu/index'], 'activeUrl'=> ['/website/menu/*']],
				],
			]
		];
	}
}