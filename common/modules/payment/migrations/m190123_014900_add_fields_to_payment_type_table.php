<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m190123_014900_add_fields_to_payment_type_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment_type}}', 'kind', $this->integer(1)->defaultValue(0)->after('id'));
		$this->addColumn('{{%payment_type}}', 'price_tax', $this->decimal(13, 2)->defaultValue(0.0)->after('price'));
	}

	public function down() {
		$this->dropColumn('{{%payment_type}}', 'kind');
		$this->dropColumn('{{%payment_type}}', 'price_tax');
	}
}