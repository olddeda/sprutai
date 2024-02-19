<?php
namespace common\modules\menu\migrations;

use yii\db\Migration;

class m190819_142100_add_field_type_to_menu_table extends Migration
{
	public function up() {
		$this->addColumn('{{%menu}}', 'type', $this->integer(1)->defaultValue(0)->after('id'));
	}

	public function down() {
		$this->dropColumn('{{%menu}}', 'type');
	}
}