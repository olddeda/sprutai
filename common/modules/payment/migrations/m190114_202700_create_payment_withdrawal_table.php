<?php
namespace common\modules\payment\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `payment_withdrawal`.
 */
class m190114_202700_create_payment_withdrawal_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%payment_withdrawal}}', [
			'id' => $this->primaryKey(),
			'payment_id' => $this->integer()->notNull(),
			'payment_source_id' => $this->integer()->notNull(),
			'module_type' => $this->integer()->defaultValue(0),
			'module_id' => $this->integer()->defaultValue(0),
			'descr' => $this->string(),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
            'date_at' => $this->integer(11)->null(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);
		
		$this->createIndex('idx-payment-withdrawal-payment_id', '{{%payment_withdrawal}}', ['payment_id']);
		$this->createIndex('idx-payment-withdrawal-payment_source_id', '{{%payment_withdrawal}}', ['payment_source_id']);
		$this->createIndex('idx-payment-withdrawal-payment_id-status', '{{%payment_withdrawal}}', ['payment_id', 'status']);
		
		$this->addForeignKey('fk_payment-withdrawal-payment_id', '{{%payment_withdrawal}}', 'payment_id', '{{%payment}}', 'id', 'CASCADE', 'RESTRICT');
		$this->addForeignKey('fk_payment-withdrawal-payment_source_id', '{{%payment_withdrawal}}', 'payment_source_id', '{{%payment}}', 'id', 'CASCADE', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		$this->dropForeignKey('fk_payment-withdrawal-payment_id', '{{%payment_withdrawal}}');
		$this->dropForeignKey('fk_payment-withdrawal_source-payment_id', '{{%payment_withdrawal}}');

		// drop table
		$this->dropTable('{{%payment_withdrawal}}');
	}
}
