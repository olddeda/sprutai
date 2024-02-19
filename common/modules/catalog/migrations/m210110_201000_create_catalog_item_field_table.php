<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

/**
 * Handles the creation for table `m210110_201000_create_catalog_item_field_table`.
 */
class m210110_201000_create_catalog_item_field_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp(): bool
    {
		// create table
		$this->createTable('{{%catalog_item_field}}', [
			'catalog_item_id' => $this->integer()->notNull(),
			'catalog_field_group_id' => $this->integer()->notNull(),
			'catalog_field_id' => $this->integer()->notNull(),
			'tag_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'format' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'value' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'identifier' => $this->string()->notNull(),
            'unit' => $this->string()->null(),
            'sequence' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
		]);

        // create pk
        $this->addPrimaryKey('fk-catalog_item_field-pk', '{{%catalog_item_field}}', ['catalog_item_id', 'catalog_field_group_id', 'catalog_field_id', 'tag_id', 'name']);
        $this->addForeignKey('fk-catalog_item_field-catalog_item_id', '{{%catalog_item_field}}', 'catalog_item_id', '{{%catalog_item}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item_field-field_group_id', '{{%catalog_item_field}}', 'catalog_field_group_id', '{{%catalog_field_group}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item_field-field_id', '{{%catalog_item_field}}', 'catalog_field_id', '{{%catalog_field}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item_field-tag_id', '{{%catalog_item_field}}', 'tag_id', '{{%tag}}', 'id', 'CASCADE', 'RESTRICT');

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown(): bool
    {
        // drop foreign keys
        $this->dropForeignKey('fk-catalog_item_field-catalog_item_id', '{{%catalog_item_field}}');
        $this->dropForeignKey('fk-catalog_item_field-field_group_id', '{{%catalog_item_field}}');
        $this->dropForeignKey('fk-catalog_item_field-field_id', '{{%catalog_item_field}}');
        $this->dropForeignKey('fk-catalog_item_field-tag_id', '{{%catalog_item_field}}');

		// drop table
		$this->dropTable('{{%catalog_item_field}}');

		return true;
	}
}
