<?php
namespace common\modules\payment\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `payment`.
 */
class m180522_140000_create_payment_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%payment}}', [
			'id' => $this->primaryKey(),
			'kind' => $this->integer(1)->defaultValue(0),
			'module_type' => $this->integer()->defaultValue(0),
			'module_id' => $this->integer()->defaultValue(0),
			'payment_type_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'provider_id' => $this->integer()->defaultValue(0),
			'provider_error' => $this->text()->defaultValue(NULL),
			'price' => $this->decimal(13, 2)->notNull(),
			'descr' => $this->text(),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
            'date_at' => $this->integer(11)->null(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);
		
		$this->createIndex('idx-payment-kind-status', '{{%payment}}', ['kind', 'status']);
		$this->createIndex('idx-payment-module_type-module_id', '{{%payment}}', ['module_type', 'module_id']);
		$this->createIndex('idx-payment-module_type-module_id-user_id', '{{%payment}}', ['module_type', 'module_id', 'user_id']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%payment}}');
	}
}
