<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `catalog_item`.
 */
class m191116_144700_create_catalog_item extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
	 
		// create table
		$this->createTable('{{%catalog_item}}', [
			'id' => $this->primaryKey(),
            'vendor_id' => $this->integer()->null(),
            'title' => $this->string()->notNull(),
            'model' => $this->string()->null(),
            'url' => $this->string()->null(),
            'comment' => $this->text()->null(),
            'system_manufacturer' => $this->string()->null(),
            'system_model' => $this->string()->null(),
            'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// create index
        $this->createIndex('idx-catalog_item-status', '{{%catalog_item}}', ['status']);

        // create foreign keys
        $this->addForeignKey('fk-catalog_item-vendor_id', '{{%catalog_item}}', 'vendor_id', '{{%tag}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item-created_by', '{{%catalog_item}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item-updated_by', '{{%catalog_item}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
	    $this->dropForeignKey('fk-catalog_item-vendor_id', '{{%catalog_item}}');
        $this->dropForeignKey('fk-catalog_item-created_by', '{{%catalog_item}}');
        $this->dropForeignKey('fk-catalog_item-updated_by', '{{%catalog_item}}');

		$this->dropTable('{{%catalog_item}}');
	}
}
