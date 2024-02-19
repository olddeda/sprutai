<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `company_user`.
 */
class m190226_181400_create_company_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
    	
    	// create table
        $this->createTable('{{%company_user}}', [
            'id' => $this->primaryKey(),
			'company_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'status' => $this->integer(3),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
        ]);
	
		// creates indexes
		$this->createIndex('idx-company_user-status', '{{%company_user}}', 'status');
		$this->createIndex('idx-company_user-company_id', '{{%company_user}}', 'company_id');
		$this->createIndex('idx-company_user-user_id', '{{%company_user}}', 'user_id');
		$this->createIndex('idx-company_user-company_id-status', '{{%company_user}}', ['company_id', 'status']);
		$this->createIndex('idx-company_user-company_id-user_id', '{{%company_user}}', ['company_id', 'user_id']);
	
		// create foreign keys
		$this->addForeignKey('fk-company_user-company', '{{%company_user}}', 'company_id', '{{%company}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk-company_user-user', '{{%company_user}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down() {
	
		// drop foreign keys
		$this->dropForeignKey('fk-company_user-company', '{{%company_user}}');
		$this->dropForeignKey('fk-company_user-user', '{{%company_user}}');
    
		// drop table
        $this->dropTable('{{%company_user}}');
    }
}
