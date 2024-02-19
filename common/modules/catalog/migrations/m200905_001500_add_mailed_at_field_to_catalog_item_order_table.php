<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m200905_001500_add_mailed_at_field_to_catalog_item_order_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item_order}}', 'mailed_at', $this->integer()->null()->after('delivery_code'));
	}

	public function down() {
		$this->dropColumn('{{%catalog_item_order}}', 'mailed_at');
	}
}