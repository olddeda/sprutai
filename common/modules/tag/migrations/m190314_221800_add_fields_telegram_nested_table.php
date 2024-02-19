<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

class m190314_221800_add_fields_telegram_nested_table extends Migration
{
	public function up() {
		$this->addColumn('{{%tag_nested}}', 'module_type', $this->integer()->after('id'));
		$this->addColumn('{{%tag_nested}}', 'module_id', $this->integer()->after('module_type'));
	}

	public function down() {
		$this->dropColumn('{{%tag_nested}}', 'module_type');
		$this->dropColumn('{{%tag_nested}}', 'module_id');
	}
}