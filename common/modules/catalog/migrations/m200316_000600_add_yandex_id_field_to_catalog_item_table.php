<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m200316_000600_add_yandex_id_field_to_catalog_item_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item}}', 'yandex_id', $this->integer()->null()->after('system_model'));
	}

	public function down() {
		$this->dropColumn('{{%catalog_item}}', 'yandex_id');
	}
}