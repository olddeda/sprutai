<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m180925_120313_add_field_to_payment_type_module_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment_type_module}}', 'price', $this->decimal(13, 2)->notNull()->defaultValue(1.0)->after('payment_type_id'));
		$this->addColumn('{{%payment_type_module}}', 'price_fixed', $this->boolean()->defaultValue(false)->after('price'));
	}

	public function down() {
		$this->dropColumn('{{%payment_type_module}}', 'price');
		$this->dropColumn('{{%payment_type_module}}', 'price_fixed');
	}
}
