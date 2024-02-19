<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `telegram_stop`.
 */
class m200520_224400_create_telegram_stop_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%telegram_stop}}', [
            'id' => $this->primaryKey(),
			'keyword' => $this->string(255)->notNull(),
			'kick' => $this->boolean()->defaultValue(true),
			'status' => $this->integer(2)->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%telegram_stop_item}}', [
            'id' => $this->primaryKey(),
            'telegram_stop_id' => $this->integer()->notNull(),
            'telegram_user_id' => $this->integer()->notNull(),
            'telegram_chat_id' => $this->integer()->notNull(),
            'text' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
		
		$this->createIndex('idx-telegram_stop-status', '{{%telegram_stop}}', 'status');

        $this->addForeignKey('fk-telegram_stop_item-telegram_stop_id', '{{%telegram_stop_item}}', 'telegram_stop_id', '{{%telegram_stop}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%telegram_stop}}');
        $this->dropTable('{{%telegram_stop_item}}');
    }
}