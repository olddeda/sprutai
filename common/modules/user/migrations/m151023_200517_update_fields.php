<?php

use yii\db\Migration;
use yii\db\Schema;

class m151023_200517_update_fields extends Migration
{
	public function up() {
		$this->addColumn('{{%user}}', 'company_id', Schema::TYPE_INTEGER.' NOT NULL AFTER id');
	}

	public function down() {
		$this->dropColumn('{{%user}}', 'company_id');
	}
}