<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

/**
 * Handles the creation for table `m200605_021600_create_catalog_item_order`.
 */
class m200605_021600_create_catalog_item_order extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp() {

		// create table
		$this->createTable('{{%catalog_item_order}}', [
			'id' => $this->primaryKey(),
			'catalog_item_id' => $this->integer()->notNull(),
			'company_id' => $this->integer()->notNull(),
			'fio' => $this->string(255)->notNull(),
			'email' => $this->string(255)->notNull(),
			'phone' => $this->string(255)->notNull(),
            'address' => $this->string(255)->null(),
            'delivery_type' => $this->integer(2)->defaultValue(0),
            'delivery_code' => $this->string()->null(),
            'licence' => $this->string(255)->null(),
            'comment' => $this->text()->null(),
            'status' => $this->integer(1)->defaultValue(0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-catalog_item_order-catalog_item_id', '{{%catalog_item_order}}', ['catalog_item_id']);
        $this->createIndex('idx-catalog_item_order-company_id', '{{%catalog_item_order}}', ['company_id']);

		// create fk
		$this->addForeignKey('fk-catalog_item_order-catalog_item_id', '{{%catalog_item_order}}', 'catalog_item_id', '{{%catalog_item}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item_order-company_id', '{{%catalog_item_order}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk-catalog_item_order-catalog_item_id', '{{%catalog_item_order}}');
			
		// drop table
		$this->dropTable('{{%catalog_item_order}}');
	}
}
