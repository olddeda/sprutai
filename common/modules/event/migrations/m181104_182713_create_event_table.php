<?php
namespace common\modules\event\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `event`.
 */
class m181104_182713_create_event_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		// create table
		$this->createTable('{{%event}}', [
			'id' => $this->primaryKey(),
			'module_type' => $this->integer()->defaultValue(0),
			'module_id' => $this->integer()->defaultValue(0),
			'user_id' => $this->integer()->notNull(),
			'text' => $this->text()->notNull(),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
            'date_at' => $this->integer()->null(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		]);
		
		// create index
		$this->createIndex('idx-event-module_type-module_id', '{{%event}}', ['module_type', 'module_id']);
		$this->createIndex('idx-event-module_type-module_id-status', '{{%event}}', ['module_type', 'module_id', 'status']);
		$this->createIndex('idx-event-module_type-module_id-user', '{{%event}}', ['module_type', 'module_id', 'user_id']);
		
		// create foreign keys
		$this->addForeignKey('fk-event-user', '{{%event}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk-event-created_by', '{{%event}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk-event-updated_by', '{{%event}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// drop foreign keys
		$this->dropForeignKey('fk-event-user', '{{%event}}');
		$this->dropForeignKey('fk-event-created_by', '{{%event}}');
		$this->dropForeignKey('fk-event-updated_by', '{{%event}}');
		
		// drop index
		$this->dropIndex('idx-event-module_type-module_id', '{{%event}}');
		$this->dropIndex('idx-event-module_type-module_id-status', '{{%event}}');
		$this->dropIndex('idx-event-module_type-module_id-user', '{{%event}}');

		// drop table
		$this->dropTable('{{%event}}');
	}
}
