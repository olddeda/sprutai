<?php
namespace common\modules\payment\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `payment_type`.
 */
class m180522_140400_create_payment_type_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%payment_type}}', [
			'id' => $this->primaryKey(),
			'title' => $this->string(255)->notNull(),
			'descr' => $this->text(),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);
		
		$this->createIndex('idx-payment_type-status', '{{%payment_type}}', ['status']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%payment_type}}');
	}
}
