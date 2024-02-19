<?php
namespace common\modules\contest\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `contest`.
 */
class m190316_152500_create_contest extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%contest}}', [
			'id' => $this->primaryKey(),
			'module_type' => $this->integer()->defaultValue(0),
			'module_id' => $this->integer()->defaultValue(0),
			'title' => $this->string(255)->notNull(),
			'is_paid' => $this->boolean()->defaultValue(false),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'date_from_at' => $this->integer(),
			'date_to_at' => $this->integer(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		]);

		// creates index for column `status`
		$this->createIndex('idx-contest-module_type-module_id-status', '{{%contest}}', ['module_type', 'module_id', 'status']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%contest}}');
	}
}
