<?php
namespace common\modules\payment\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `payment_type_module`.
 */
class m180522_140900_create_payment_type_module_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%payment_type_module}}', [
			'id' => $this->primaryKey(),
			'module_type' => $this->integer()->notNull(),
			'module_id' => $this->integer()->notNull(),
			'payment_type_id' => $this->integer()->notNull(),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);
		
		$this->createIndex('idx-payment_type_module-module-type-status', '{{%payment_type_module}}', ['module_type', 'module_id', 'payment_type_id']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%payment_type_module}}');
	}
}
