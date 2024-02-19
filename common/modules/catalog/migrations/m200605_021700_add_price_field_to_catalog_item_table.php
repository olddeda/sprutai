<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m200605_021700_add_price_field_to_catalog_item_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item}}', 'price', $this->decimal(10,2)->defaultValue(0)->after('yandex_id'));
	}

	public function down() {
		$this->dropColumn('{{%catalog_item}}', 'price');
	}
}