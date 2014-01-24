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
			'text' => ($this->db->driverName === 'mysql' ? 'longtext' : Schema::TYPE_TEXT).' NOT NULL',

			'meta_keywords' => Schema::TYPE_STRING.'(255)',
			'meta_description' => Schema::TYPE_STRING.'(255)',

			'layout' => Schema::TYPE_STRING.'(50)',

			'created_at' => Schema::TYPE_INTEGER,
			'created_by_user_id' => Schema::TYPE_INTEGER,
			'updated_at' => Schema::TYPE_INTEGER,
			'updated_by_user_id' => Schema::TYPE_INTEGER,
		], $tableOptions);

		$this->createIndex($this->db->tablePrefix.'website_page_slug_ix', '{{%website_page}}', 'slug');
	}

	public function safeDown()
	{
		$this->dropIndex($this->db->tablePrefix.'website_page_slug_ix', '{{%website_page}}');
		$this->dropTable('{{%website_page}}');
	}
}
