<?php

use yii\db\Schema;

class m140109_134232_create_page_tables extends \yii\db\Migration
{
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%website_page}}', [
			'id' => Schema::TYPE_PK,
			'slug' => Schema::TYPE_STRING.'(150) NOT NULL',
			'locale' => Schema::TYPE_STRING.'(10)',
			'published' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 3',

			'title' => Schema::TYPE_STRING.'(150) NOT NULL',
			'window_title' => Schema::TYPE_STRING.'(150)',
			'text' => Schema::TYPE_TEXT.' NOT NULL',

			'meta_keywords' => Schema::TYPE_STRING.'(255)',
			'meta_description' => Schema::TYPE_STRING.'(255)',

			'layout' => Schema::TYPE_STRING.'(50)',

			'create_time' => Schema::TYPE_INTEGER,
			'create_user_id' => Schema::TYPE_INTEGER,
			'update_time' => Schema::TYPE_INTEGER,
			'update_user_id' => Schema::TYPE_INTEGER,
		], $tableOptions);

		$this->createIndex($this->db->tablePrefix.'website_menu_slug_ix', '{{%website_page}}', 'slug');
	}

	public function safeDown()
	{
		$this->dropIndex($this->db->tablePrefix.'website_menu_slug_ix', '{{%website_page}}');
		$this->dropTable('{{%website_page}}');
	}
}
