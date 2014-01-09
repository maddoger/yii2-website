<?php

use yii\db\Schema;

class m131230_084441_create_menu_table extends \yii\db\Migration
{
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%website_menu}}', [
			'id' => Schema::TYPE_PK,
			'parent_id' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
			'link' => Schema::TYPE_STRING.'(150) NOT NULL',
			'preg' => Schema::TYPE_STRING.'(150) NOT NULL',
			'title' => Schema::TYPE_STRING.'(150) NOT NULL',
			'css_class' => Schema::TYPE_STRING.'(150) NOT NULL',
			'enabled' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT TRUE',
			'sort' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',

			'create_time' => Schema::TYPE_INTEGER.' NOT NULL',
			'create_user_id' => Schema::TYPE_INTEGER.' NOT NULL',
			'update_time' => Schema::TYPE_INTEGER.' NOT NULL',
			'update_user_id' => Schema::TYPE_INTEGER.' NOT NULL',
		], $tableOptions);

		$this->createIndex($this->db->tablePrefix.'website_menu_sort_ix', '{{%website_menu}}', 'sort');
		$this->addForeignKey($this->db->tablePrefix.'website_menu_parent_id_fk', '{{%website_menu}}', 'parent_id',
			'{{%website_menu}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function safeDown()
	{
		$this->dropForeignKey($this->db->tablePrefix.'website_menu_parent_id_fk', '{{%website_menu}}');
		$this->dropIndex($this->db->tablePrefix.'website_menu_sort_ix', '{{%website_menu}}');
		$this->dropTable('{{%website_menu}}');
	}
}
