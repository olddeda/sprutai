<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `telegram_chat_user`.
 */
class m200610_033400_create_telegram_chat_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%telegram_chat_user}}', [
            'id' => $this->primaryKey(),
			'chat_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'number' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
            'date_at' => $this->integer()->notNull(),
			'params' => $this->text()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%telegram_chat_user}}');
    }
}