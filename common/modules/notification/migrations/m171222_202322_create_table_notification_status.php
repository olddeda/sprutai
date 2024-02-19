<?php
namespace common\modules\notification\migrations;

use yii\db\Migration;

/**
 * Class m171222_202322_create_table_notification_status
 * @package common\modules\notification\migrations
 */
class m171222_202322_create_table_notification_status extends Migration
{
	/**
	 * @inheritdoc
	 */
    public function up() {
        $this->createTable('{{%notification_status}}', [
            'id' => $this->primaryKey(),
            'provider' => $this->string(),
            'event' => $this->string(),
            'params' => $this->text(),
            'status' => $this->string()->null(),
			'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }
	
	/**
	 * @inheritdoc
	 */
    public function down() {
        $this->dropTable('{{%notification_status}}');
    }
}
