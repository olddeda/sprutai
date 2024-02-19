<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `telegram_user`.
 */
class m171121_191626_create_telegram_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%telegram_user}}', [
            'id' => $this->primaryKey(),
			'username' => $this->string(255)->notNull(),
			'first_name' => $this->string(255)->null()->defaultValue(null),
			'last_name' => $this->string(255)->null()->defaultValue(null),
			'params' => $this->text()->null(),
			'status' => $this->integer(2)->defaultValue(0),
			'lastvisit_at' => $this->integer()->null(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
		
		$this->createIndex('idx-telegram_user-status', '{{%telegram_user}}', 'status');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%telegram_user}}');
    }
}