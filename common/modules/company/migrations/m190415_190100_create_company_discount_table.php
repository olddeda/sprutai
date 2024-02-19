<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `company_discount`.
 */
class m190415_190100_create_company_discount_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
    	
    	// create table
        $this->createTable('{{%company_discount}}', [
            'id' => $this->primaryKey(),
			'company_id' => $this->integer()->notNull(),
			'promocode' => $this->string()->notNull(),
			'descr' => $this->text(500)->null(),
			'discount' => $this->integer()->notNull(),
			'infinitely' => $this->boolean()->defaultValue(true),
			'status' => $this->integer(3),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'date_start_at' => $this->integer()->null(),
			'date_end_at' => $this->integer()->null(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
        ]);
	
		// creates indexes
		$this->createIndex('idx-company_discount-status', '{{%company_discount}}', 'status');
		$this->createIndex('idx-company_discount-company_id', '{{%company_discount}}', 'company_id');
		$this->createIndex('idx-company_discount-company_id-status', '{{%company_discount}}', ['company_id', 'status']);
	
		// create foreign keys
		$this->addForeignKey('fk-company_discount-company', '{{%company_discount}}', 'company_id', '{{%company}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down() {
	
		// drop foreign keys
		$this->dropForeignKey('fk-company_discount-company', '{{%company_discount}}');
    
		// drop table
        $this->dropTable('{{%company_discount}}');
    }
}
