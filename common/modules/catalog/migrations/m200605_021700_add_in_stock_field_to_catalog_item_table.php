<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m200605_021700_add_in_stock_field_to_catalog_item_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item}}', 'in_stock', $this->integer()->defaultValue(0)->after('yandex_id'));
	}

	public function down() {
		$this->dropColumn('{{%catalog_item}}', 'in_stock');
	}
}