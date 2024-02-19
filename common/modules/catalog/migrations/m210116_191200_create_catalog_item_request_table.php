<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

/**
 * Handles the creation for table `m210116_191200_create_catalog_item_request_table`.
 */
class m210116_191200_create_catalog_item_request_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp(): bool
    {
		// create table
		$this->createTable('{{%catalog_item_correct}}', [
            'id' => $this->primaryKey(),
			'catalog_item_id' => $this->integer()->notNull(),
			'type' => $this->integer(2)->notNull(),
			'action' => $this->integer(2)->notNull(),
            'value' => $this->text(),
            'status' => $this->integer(1)->defaultValue(0),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
		]);

        // create pk
        $this->addForeignKey('fk-catalog_item_correct-catalog_item_id', '{{%catalog_item_correct}}', 'catalog_item_id', '{{%catalog_item}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item_correct-created_by', '{{%catalog_item_correct}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('fk-catalog_item_correct-updated_by', '{{%catalog_item_correct}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        // create index
        $this->createIndex('idx-catalog_item_correct-catalog_item_id-status', '{{%catalog_item_correct}}', ['catalog_item_id', 'status']);

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown(): bool
    {
        // drop foreign keys
        $this->dropForeignKey('fk-catalog_item_correct-catalog_item_id', '{{%catalog_item_correct}}');

		// drop table
		$this->dropTable('{{%catalog_item_correct}}');

		return true;
	}
}
