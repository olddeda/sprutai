<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

class m200223_205100_add_fields_to_company_discount_table extends Migration
{
	public function up() {
		$this->addColumn('{{%company_discount}}', 'discount_to', $this->integer()->defaultValue(null)->after('discount'));
	}

	public function down() {
		$this->dropColumn('{{%company_discount}}', 'discount_to');
	}
}
