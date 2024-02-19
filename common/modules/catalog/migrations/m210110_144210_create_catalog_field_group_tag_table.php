<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

/**
 * Handles the creation for table `m210110_144210_create_catalog_field_group_tag_table`.
 */
class m210110_144210_create_catalog_field_group_tag_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp(): bool
    {
		// create table
		$this->createTable('{{%catalog_field_group_tag}}', [
			'catalog_field_group_id' => $this->integer()->notNull(),
			'tag_id' => $this->integer()->notNull(),
		]);

        // create pk
        $this->addPrimaryKey('fk-catalog_field_group_tag-primary', '{{%catalog_field_group_tag}}', ['catalog_field_group_id', 'tag_id']);
        $this->addForeignKey('fk-catalog_field_group_tag-group_id', '{{%catalog_field_group_tag}}', 'catalog_field_group_id', '{{%catalog_field_group}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-catalog_field_group_tag-tag_id', '{{%catalog_field_group_tag}}', 'tag_id', '{{%tag}}', 'id', 'CASCADE', 'RESTRICT');

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown(): bool
    {
        // drop foreign keys
        $this->dropForeignKey('fk-catalog_field_group_tag-primary', '{{%catalog_field_group_tag}}');
        $this->dropForeignKey('fk-catalog_field_group_tag-group_id', '{{%catalog_field_group_tag}}');
        $this->dropForeignKey('fk-catalog_field_group_tag-tag_id', '{{%catalog_field_group_tag}}');

		// drop table
		$this->dropTable('{{%catalog_field_group_tag}}');

		return true;
	}
}
