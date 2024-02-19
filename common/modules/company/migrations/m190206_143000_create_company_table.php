<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `company`.
 */
class m190206_143000_create_company_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
    	
    	// create table
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
			'type' => $this->integer(2)->defaultValue(0),
			'status' => $this->integer(2)->defaultValue(0),
            'title' => $this->string(255)->notNull(),
            'descr' => $this->text()->notNull(),
            'text' => $this->text()->notNull(),
			'site' => $this->string(255)->null(),
			'email' => $this->string(255)->null(),
			'phone' => $this->string(255)->null(),
			'status' => $this->integer(3),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
			'published_at' => $this->integer(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);
	
		// creates indexes
		$this->createIndex('idx-company-status', '{{%company}}', 'status');
    }

    /**
     * @inheritdoc
     */
    public function down() {
    
		// drop table
        $this->dropTable('{{%company}}');
    }
}
