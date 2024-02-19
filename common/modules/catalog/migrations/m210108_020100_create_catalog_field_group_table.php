<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

/**
 * Handles the creation for table `m210108_020100_create_catalog_field_group_table`.
 */
class m210108_020100_create_catalog_field_group_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp(): bool
    {
		// create table
		$this->createTable('{{%catalog_field_group}}', [
			'id' => $this->primaryKey(),
			'title' => $this->string(255)->notNull(),
            'status' => $this->integer(1)->defaultValue(0),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-catalog_field_group-status', '{{%catalog_field_group}}', ['status']);

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown(): bool
    {
			
		// drop table
		$this->dropTable('{{%catalog_field_group}}');

		return true;
	}
}
