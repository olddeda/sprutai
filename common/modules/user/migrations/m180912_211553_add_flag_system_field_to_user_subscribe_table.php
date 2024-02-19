<?php

use yii\db\Schema;
use yii\db\Migration;

class m180912_211553_add_flag_system_field_to_user_subscribe_table extends Migration
{
	public function up() {
		$this->addColumn('{{%user_subscribe}}', 'flag_system', $this->integer(2)->defaultValue(1)->after('user_id'));
	}

	public function down() {
		$this->dropColumn('{{%user_profile}}', 'flag_system');
	}
}
