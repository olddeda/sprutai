<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m180926_175213_add_field_to_payment_type_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment_type}}', 'identifier', $this->string(255)->unique()->defaultValue(null)->after('descr'));
		$this->addColumn('{{%payment_type}}', 'price', $this->decimal(13, 2)->notNull()->defaultValue(1.0)->after('identifier'));
		$this->addColumn('{{%payment_type}}', 'price_fixed', $this->boolean()->defaultValue(false)->after('price'));
	}

	public function down() {
		$this->dropColumn('{{%payment_type}}', 'identifier');
		$this->dropColumn('{{%payment_type}}', 'price');
		$this->dropColumn('{{%payment_type}}', 'price_fixed');
	}
}
