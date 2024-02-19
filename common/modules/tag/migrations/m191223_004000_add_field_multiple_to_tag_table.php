<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

class m191223_004000_add_field_multiple_to_tag_table extends Migration
{
	public function up() {
		$this->addColumn('{{%tag}}', 'multiple', $this->boolean()->defaultValue(false)->after('sequence'));
	}

	public function down() {
		$this->dropColumn('{{%tag}}', 'multiple');
	}
}