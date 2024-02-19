<?php
use yii\db\Migration;

/**
 * Handles the creation for table `user_activity`.
 */
class m200504_232000_create_user_activity_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->createTable('{{%user_activity}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull(),
			'module_type' => $this->integer()->notNull(),
			'module_id' => $this->integer()->notNull(),
            'parent_module_type' => $this->integer()->null(),
            'parent_module_id' => $this->integer()->null(),
            'user_id' => $this->integer()->notNull(),
            'from_user_id' => $this->integer()->notNull(),
            'date_at' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-user_activity-module_type-module_id', '{{%user_activity}}', ['module_type', 'module_id']);
        $this->createIndex('idx-user_activity-module_type-user_id', '{{%user_activity}}', ['module_type', 'user_id']);
        $this->createIndex('idx-user_activity-user_id', '{{%user_activity}}', ['user_id']);
        $this->createIndex('idx-user_activity-from_user_id', '{{%user_activity}}', ['from_user_id']);

        $this->addForeignKey('fk-user_activity-user', '{{%user_activity}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk-user_activity-from_user', '{{%user_activity}}', 'from_user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropForeignKey('fk-user_activity-user', '{{%user_activity}}');
        $this->dropForeignKey('fk-user_activity-from_user', '{{%user_activity}}');

        $this->dropTable('{{%user_activity}}');
    }
}
