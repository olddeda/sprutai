<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m200114_034100_add_field_data_to_catalog_item_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item}}', 'data', $this->text()->null()->after('system_model'));
	}

	public function down() {
		$this->dropColumn('{{%catalog)item}}', 'data');
	}
}