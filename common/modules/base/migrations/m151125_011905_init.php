<?php

use yii\db\Schema;
use yii\db\Migration;

class m151125_011905_init extends Migration
{
    public function up() {
		$this->createTable('{{%session}}', [
			'id' => $this->string(40),
			'expire' => $this->integer(),
			'data' => $this->binary(),
		]);

		$this->addPrimaryKey('session_id', '{{%session}}', 'id');
    }

    public function down() {
		$this->dropTable('{{%session}}');
    }
}
