<?php

use yii\db\Migration;
use yii\db\Schema;

class m141025_224648_website_menu extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%website_menu}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER,
            'type' => Schema::TYPE_SMALLINT,
            'label' => Schema::TYPE_STRING . '(150) NOT NULL',
            'title' => Schema::TYPE_STRING . '(150)',
            'language' => Schema::TYPE_STRING . '(10)',
            'slug' => Schema::TYPE_STRING . '(20)',
            'link' => Schema::TYPE_STRING . '(150)',
            'preg' => Schema::TYPE_STRING . '(150)',
            'target' => Schema::TYPE_STRING . '(50)',
            'css_class' => Schema::TYPE_STRING . '(50)',
            'icon_class' => Schema::TYPE_STRING . '(50)',
            'element_id' => Schema::TYPE_STRING . '(50)',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'sort' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'page_id' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'updated_by' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex($this->db->tablePrefix . 'website_menu_slug_ix',
            '{{%website_menu}}', 'slug');

        $this->addForeignKey($this->db->tablePrefix . 'website_menu_parent_fk',
            '{{%website_menu}}', 'parent_id',
            '{{%website_menu}}', 'id',
            'CASCADE', 'CASCADE');

        $this->addForeignKey($this->db->tablePrefix . 'website_menu_page_fk',
            '{{%website_menu}}', 'page_id',
            '{{%website_page}}', 'id',
            'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey($this->db->tablePrefix . 'website_menu_parent_fk', '{{%website_menu}}');
        $this->dropForeignKey($this->db->tablePrefix . 'website_menu_page_fk', '{{%website_menu}}');
        $this->dropIndex($this->db->tablePrefix . 'website_menu_slug_ix', '{{%website_menu}}');
        $this->dropTable('{{%website_menu}}');
    }
}
