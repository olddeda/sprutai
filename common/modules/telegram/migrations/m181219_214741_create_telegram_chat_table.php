<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `telegram_chat`.
 */
class m181219_214741_create_telegram_chat_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%telegram_chat}}', [
            'id' => $this->primaryKey(),
			'title' => $this->string(255)->notNull(),
			'identifier' => $this->string(255)->notNull(),
			'notify_content' => $this->boolean()->defaultValue(false),
			'notify_payment' => $this->boolean()->defaultValue(false),
			'status' => $this->integer(2)->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
		
		$this->createIndex('idx-telegram_chat-status', '{{%telegram_chat}}', 'status');
		$this->createIndex('idx-telegram_chat-status-content', '{{%telegram_chat}}', ['status', 'notify_content']);
		$this->createIndex('idx-telegram_chat-status-payment', '{{%telegram_chat}}', ['status', 'notify_payment']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%telegram_chat}}');
    }
}