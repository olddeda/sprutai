<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

/**
 * Handles the creation for table `m210108_154200_create_catalog_field_table`.
 */
class m210108_154200_create_catalog_field_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp(): bool
    {
		// create table
		$this->createTable('{{%catalog_field}}', [
			'id' => $this->primaryKey(),
			'catalog_field_group_id' => $this->integer()->notNull(),
			'type' => $this->integer(2)->defaultValue(0),
			'format' => $this->integer(2)->defaultValue(0),
			'title' => $this->string(255)->notNull(),
            'identifier' => $this->string(255)->notNull(),
            'unit' => $this->string(255)->null(),
            'sequence' => $this->integer()->defaultValue(0),
            'status' => $this->integer(1)->defaultValue(0),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-catalog_field-group_id-status', '{{%catalog_field}}', ['catalog_field_group_id', 'status']);

        // create pk
        $this->addForeignKey('fk-catalog_field-group_id', '{{%catalog_field}}', 'catalog_field_group_id', '{{%catalog_field_group}}', 'id', 'CASCADE', 'RESTRICT');

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown(): bool
    {
        // drop foreign keys
        $this->dropForeignKey('fk-catalog_field-group_id', '{{%catalog_field}}');

		// drop table
		$this->dropTable('{{%catalog_field_group}}');

		return true;
	}
}
