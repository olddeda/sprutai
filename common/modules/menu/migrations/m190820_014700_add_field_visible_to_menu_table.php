<?php
namespace common\modules\menu\migrations;

use yii\db\Migration;

class m190820_014700_add_field_visible_to_menu_table extends Migration
{
	public function up() {
		$this->addColumn('{{%menu}}', 'visible', $this->integer(1)->defaultValue(1)->after('status'));
	}

	public function down() {
		$this->dropColumn('{{%menu}}', 'visible');
	}
}