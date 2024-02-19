<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m180831_163309_add_physical_field_to_payment_type_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment_type}}', 'physical', $this->boolean()->defaultValue(false)->after('descr'));
	}

	public function down() {
		$this->dropColumn('{{%payment_type}}', 'physical');
	}
}
