<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m180926_202401_add_field_to_payment_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment}}', 'to_user_id', $this->integer()->defaultValue(null)->after('user_id'));
	}

	public function down() {
		$this->dropColumn('{{%payment}}', 'to_user_id');
	}
}