<?php

use yii\db\Schema;
use yii\db\Migration;

class m200701_184300_add_fields_to_user_profile_table extends Migration
{
	public function up() {
		$this->addColumn('{{%user_profile}}', 'wallet_type', $this->integer()->null()->after('phone'));
		$this->addColumn('{{%user_profile}}', 'wallet_number', $this->string(100)->null()->after('wallet_type'));
	}

	public function down() {
		$this->dropColumn('{{%user_profile}}', 'wallet_type');
		$this->dropColumn('{{%user_profile}}', 'wallet_number');
	}
}
