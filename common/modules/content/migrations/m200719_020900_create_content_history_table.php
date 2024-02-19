<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation for table `content_history`.
 */
class m200719_020900_create_content_history_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp() {

		// create table
		$this->createTable('{{%content_history}}', [
			'id' => $this->primaryKey(),
			'content_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'json' => $this->string()->notNull(),
			'status' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_history-content_id', '{{%content_history}}', ['content_id']);
		$this->createIndex('idx-content_history-content_id-user_id', '{{%content_history}}', ['content_id', 'user_id']);
		$this->createIndex('idx-content_history-status', '{{%content_history}}', ['status']);
		
		// create pk
		$this->addForeignKey('fk-content_history-content', '{{%content_history}}', 'content_id', '{{%content}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk-content_history-user', '{{%content_history}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk-content_history-content', '{{%content_history}}');
        $this->dropForeignKey('fk-content_history-user', '{{%user}}');
			
		// drop table
		$this->dropTable('{{%content_history}');
	}
}
