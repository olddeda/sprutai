<?php
namespace common\modules\payment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m181011_223615_add_field_to_payment_table extends Migration
{
	public function up() {
		$this->addColumn('{{%payment}}', 'comment', $this->text()->defaultValue(null)->after('descr'));
	}

	public function down() {
		$this->dropColumn('{{%payment}}', 'comment');
	}
}