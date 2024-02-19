<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m190117_005100_add_field_to_payment_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment}}', 'tax', $this->decimal(13, 2)->defaultValue(0.0)->after('price'));
	}

	public function down() {
		$this->dropColumn('{{%payment}}', 'tax');
	}
}