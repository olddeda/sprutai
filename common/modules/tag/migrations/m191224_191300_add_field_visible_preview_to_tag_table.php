<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

class m191224_191300_add_field_visible_preview_to_tag_table extends Migration
{
	public function up() {
		$this->addColumn('{{%tag}}', 'visible_preview', $this->boolean()->defaultValue(false)->after('multiple'));
	}

	public function down() {
		$this->dropColumn('{{%tag}}', 'visible_preview');
	}
}