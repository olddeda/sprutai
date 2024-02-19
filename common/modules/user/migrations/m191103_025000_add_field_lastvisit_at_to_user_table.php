<?php
use yii\db\Schema;
use yii\db\Migration;

class m191103_025000_add_field_lastvisit_at_to_user_table extends Migration
{
	public function up() {
		$this->addColumn('{{%user}}', 'lastvisit_at', $this->integer()->defaultValue(null)->after('confirmed_at'));
	}

	public function down() {
		$this->dropColumn('{{%user}}', 'lastvisit_at');
	}
}
