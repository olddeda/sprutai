<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `company_catalog_item`.
 */
class m201022_195700_create_company_catalog_item_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
    	
    	// create table
        $this->createTable('{{%company_catalog_item}}', [
			'company_id' => $this->integer(2)->notNull(),
            'catalog_item_id' => $this->integer(2)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);
	
		// creates indexes
		$this->createIndex('idx-company_catalog_item', '{{%company_catalog_item}}', ['company_id', 'catalog_item_id'], true);

        // create foreign keys
        $this->addForeignKey('fk-company_catalog_item-company', '{{%company_catalog_item}}', 'company_id', '{{%company}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk-company_catalog_item-catalog_item', '{{%company_catalog_item}}', 'catalog_item_id', '{{%catalog_item}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down() {

        // drop foreign keys
        $this->dropForeignKey('fk-company_catalog_item-company', '{{%company_catalog_item}}');
        $this->dropForeignKey('fk-company_catalog_item-catalog_item', '{{%company_catalog_item}}');

        // drop table
    
		// drop table
        $this->dropTable('{{%company_catalog_item}}');
    }
}
