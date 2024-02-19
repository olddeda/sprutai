<?php
namespace common\modules\comments\migrations;

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m010101_100001_init_comment
 */
class m010101_100001_init_comment extends Migration
{
    /**
     * Create table `comment`
     */
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
			'module_type' => $this->integer()->notNull(),
			'entity' => $this->char(10)->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->null(),
            'level' => $this->tinyInteger(3)->notNull()->defaultValue(1),
            'content' => $this->text()->notNull(),
            'related_to' => $this->string(500)->notNull(),
			'status' => $this->tinyInteger(2)->notNull()->defaultValue(1),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-comment-entity', '{{%comment}}', 'entity');
        $this->createIndex('idx-comment-status', '{{%comment}}', 'status');
    }

    /**
     * Drop table `comment`
     */
    public function down() {
        $this->dropTable('{{%comment}}');
    }

}