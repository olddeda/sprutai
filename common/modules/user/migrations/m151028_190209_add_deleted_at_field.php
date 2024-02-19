<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_190209_add_deleted_at_field extends Migration
{
	public function up() {
		$this->addColumn('{{%user}}', 'deleted_at', Schema::TYPE_INTEGER.'(11) NULL AFTER blocked_at');
	}

	public function down() {
		$this->dropColumn('{{%user}}', 'deleted_at');
	}
}
