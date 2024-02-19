<?php

use yii\db\Schema;
use yii\db\Migration;

class m190122_195900_add_field_data_to_user_table extends Migration
{
	public function up() {
		$this->addColumn('{{%user}}', 'data', $this->string()->defaultValue(null)->after('auth_key'));
	}

	public function down() {
		$this->dropColumn('{{%user}}', 'data');
	}
}
