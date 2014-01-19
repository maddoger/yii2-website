<?php

use yii\db\Schema;

class m140113_084237_create_route_table extends \yii\db\Migration
{
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%website_structure}}', [
			'id' => Schema::TYPE_PK,
			'parent_id' => Schema::TYPE_INTEGER,
			'slug' => Schema::TYPE_STRING.'(150) NOT NULL',
			'type' => Schema::TYPE_STRING.'(150) NOT NULL',
			'title' => Schema::TYPE_STRING.'(150) NOT NULL',
			'params' => Schema::TYPE_STRING.'(150) NOT NULL',
			'enabled' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT TRUE',
			'sort' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',

			'created_at' => Schema::TYPE_INTEGER,
			'created_by_user_id' => Schema::TYPE_INTEGER,
			'updated_at' => Schema::TYPE_INTEGER,
			'updated_by_user_id' => Schema::TYPE_INTEGER,
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
