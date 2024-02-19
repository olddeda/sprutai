<?php

use yii\db\Migration;
use yii\db\Schema;

class m151023_155417_add_profile_fields extends Migration
{
	public function up() {
		$this->alterColumn('{{%user_profile}}', 'name', Schema::TYPE_STRING.'(50) NOT NULL');
		$this->renameColumn('{{%user_profile}}', 'name', 'first_name');

		$this->addColumn('{{%user_profile}}', 'last_name', Schema::TYPE_STRING.'(50) NOT NULL');
		$this->addColumn('{{%user_profile}}', 'middle_name', Schema::TYPE_STRING.'(50) NOT NULL');

		$this->dropColumn('{{%user_profile}}', 'public_email');
		$this->dropColumn('{{%user_profile}}', 'gravatar_email');
		$this->dropColumn('{{%user_profile}}', 'gravatar_id');
		$this->dropColumn('{{%user_profile}}', 'location');
		$this->dropColumn('{{%user_profile}}', 'website');
		$this->dropColumn('{{%user_profile}}', 'bio');
	}

	public function down() {
		$this->renameColumn('{{%user_profile}}', 'first_name', 'name');
		$this->alterColumn('{{%user_profile}}', 'name', Schema::TYPE_STRING.'(50)');

		$this->dropColumn('{{%user_profile}}', 'last_name');
		$this->dropColumn('{{%user_profile}}', 'middle_name');

		$this->addColumn('{{%user_profile}}', 'public_email', Schema::TYPE_STRING.'(255)');
		$this->addColumn('{{%user_profile}}', 'gravatar_email', Schema::TYPE_STRING.'(255)');
		$this->addColumn('{{%user_profile}}', 'gravatar_id', Schema::TYPE_STRING.'(32)');
		$this->addColumn('{{%user_profile}}', 'location', Schema::TYPE_STRING.'(255)');
		$this->addColumn('{{%user_profile}}', 'website', Schema::TYPE_STRING.'(255)');
		$this->addColumn('{{%user_profile}}', 'bio', Schema::TYPE_TEXT);
	}
}