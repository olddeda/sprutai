<?php

use yii\db\Schema;
use yii\db\Migration;

class m151026_161919_add_profile_fields extends Migration
{
	public function up() {
		$this->addColumn('{{%user_profile}}', 'phone', Schema::TYPE_STRING.'(50) NOT NULL');
	}

	public function down() {
		$this->dropColumn('{{%user_profile}}', 'phone');
	}
}
