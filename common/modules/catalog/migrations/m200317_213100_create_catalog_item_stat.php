<?php
namespace common\modules\catalog\migrations;

use common\modules\base\helpers\enum\Status;
use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;
use yii\db\Migration;

/**
 * Handles the creation for table `catalog_item_stat`.
 */
class m200317_213100_create_catalog_item_stat extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function safeUp() {

		// create table
		$this->createTable('{{%catalog_item_stat}}', [
			'id' => $this->primaryKey(),
			'catalog_item_id' => $this->integer()->notNull(),
			'comments' => $this->integer()->defaultValue(0),
			'likes' => $this->integer()->defaultValue(0),
			'favorites' => $this->integer()->defaultValue(0),
            'favorite_have' => $this->integer()->defaultValue(0),
            'contents' => $this->integer()->defaultValue(0),
            'videos' => $this->integer()->defaultValue(0),
            'rating' => $this->double(10, 2)->defaultValue(0.0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-catalog_item_stat-catalog_item_id', '{{%catalog_item_stat}}', ['catalog_item_id']);

		// create pk
		$this->addForeignKey('fk-catalog_item_stat-catalog_item_id', '{{%catalog_item_stat}}', 'catalog_item_id', '{{%catalog_item}}', 'id', 'CASCADE', 'RESTRICT');
		
		$models = CatalogItem::find()
			->joinWith(['comments'])
			->votes()
			->where(['not in', CatalogItem::tableName().'.status', [Status::TEMP, Status::DELETED]])
			->all();
		
		foreach ($models as $model) {
			CatalogItemStat::updateLinks($model);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function safeDown() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk-catalog_item_stat-catalog_item_id', '{{%catalog_item_stat}}');
			
		// drop table
		$this->dropTable('{{%catalog_item_stat}}');
	}
}
