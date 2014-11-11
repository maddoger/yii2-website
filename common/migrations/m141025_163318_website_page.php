<?php

use yii\db\Migration;
use yii\db\Schema;

class m141025_163318_website_page extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%website_page}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER,
            'slug' => Schema::TYPE_STRING . '(150) NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'layout' => Schema::TYPE_STRING . '(50)',
            'default_language' => Schema::TYPE_STRING . '(10)',
            'created_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'updated_by' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex($this->db->tablePrefix .'website_page_slug_ix', '{{%website_page}}', 'slug');

        $this->createTable('{{%website_page_i18n}}', [
            'id' => Schema::TYPE_PK,
            'page_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'language' => Schema::TYPE_STRING . '(10) NOT NULL',
            'title' => Schema::TYPE_STRING . '(150) NOT NULL',
            'window_title' => Schema::TYPE_STRING . '(150)',
            'text_format' => Schema::TYPE_STRING.'(20)',
            'text_source' => ($this->db->driverName === 'mysql' ? 'longtext' : Schema::TYPE_TEXT),
            'text' => ($this->db->driverName === 'mysql' ? 'longtext' : Schema::TYPE_TEXT),
            'meta_keywords' => Schema::TYPE_STRING . '(255)',
            'meta_description' => Schema::TYPE_STRING . '(255)',
            'meta_data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'updated_by' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex($this->db->tablePrefix .'website_page_i18n_uq',
            '{{%website_page_i18n}}', ['page_id', 'language'], true);

        $this->addForeignKey($this->db->tablePrefix .'website_page_i18n_page_fk',
            '{{%website_page_i18n}}', 'page_id',
            '{{%website_page}}', 'id',
            'CASCADE', 'CASCADE'
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey($this->db->tablePrefix.'website_page_fk', '{{%website_page_i18n}}');
        $this->dropIndex($this->db->tablePrefix .'website_page_i18n_uq', '{{%website_page_i18n}}');
        $this->dropTable('{{%website_page_i18n}}');

        $this->dropIndex($this->db->tablePrefix .'website_page_slug_ix', '{{%website_page}}');
        $this->dropTable('{{%website_page}}');
    }
}
