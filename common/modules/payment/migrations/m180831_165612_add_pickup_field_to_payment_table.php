<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m180831_165612_add_pickup_field_to_payment_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment}}', 'pickup', $this->boolean()->defaultValue(false)->after('descr'));
	}

	public function down() {
		$this->dropColumn('{{%payment}}', 'pickup');
	}
}
