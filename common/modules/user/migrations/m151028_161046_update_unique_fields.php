<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_161046_update_unique_fields extends Migration
{
	public function up() {
		$this->dropIndex('user_unique_username', '{{%user}}');
		$this->dropIndex('user_unique_email', '{{%user}}');

		$this->createIndex('user_unique_username', '{{%user}}', ['username', 'company_id'], true);
		$this->createIndex('user_unique_email', '{{%user}}', ['email', 'company_id'], true);
	}

	public function down() {
		$this->createIndex('user_unique_username', '{{%user}}', 'username', true);
		$this->createIndex('user_unique_email', '{{%user}}', 'email', true);
	}
}
