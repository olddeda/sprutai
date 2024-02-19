<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m181123_141415_add_field_to_payment_type_module_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment_type_module}}', 'price_free', $this->boolean()->defaultValue(false)->after('price_fixed'));
	}

	public function down() {
		$this->dropColumn('{{%payment_type_module}}', 'price_free');
	}
}