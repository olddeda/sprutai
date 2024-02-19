<?php

use yii\db\Schema;
use yii\db\Migration;

use common\modules\user\models\User;
use common\modules\user\models\UserSubscribe;

class m180516_020000_create_subscribe_table extends Migration
{
	public function up() {
		$this->createTable('{{%user_subscribe}}', [
			'user_id' => $this->primaryKey(),
			'flag_system' => $this->integer(2)->defaultValue(1),
			'flag_author' => $this->integer(2)->defaultValue(1),
			'flag_article' => $this->integer(2)->defaultValue(1),
			'flag_item' => $this->integer(2)->defaultValue(1),
			'flag_comment' => $this->integer(2)->defaultValue(1),
			'flag_vote' => $this->integer(2)->defaultValue(1),
			'flag_qa' => $this->integer(2)->defaultValue(1),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
		
		$this->addForeignKey('fk_user_subscribe', '{{%user_subscribe}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
		
		$this->createIndex('idx-user_subscribe-user_id', '{{%user_subscribe}}', ['user_id']);
		$this->createIndex('idx-user_subscribe-user_id-flag_system', '{{%user_subscribe}}', ['user_id', 'flag_system']);
		$this->createIndex('idx-user_subscribe-user_id-flag_author', '{{%user_subscribe}}', ['user_id', 'flag_author']);
		$this->createIndex('idx-user_subscribe-user_id-flag_article', '{{%user_subscribe}}', ['user_id', 'flag_article']);
		$this->createIndex('idx-user_subscribe-user_id-flag_item', '{{%user_subscribe}}', ['user_id', 'flag_item']);
		$this->createIndex('idx-user_subscribe-user_id-flag_comment', '{{%user_subscribe}}', ['user_id', 'flag_comment']);
		$this->createIndex('idx-user_subscribe-user_id-flag_vote', '{{%user_subscribe}}', ['user_id', 'flag_vote']);
		$this->createIndex('idx-user_subscribe-user_id-flag_qa', '{{%user_subscribe}}', ['user_id', 'flag_qa']);
		
		$users = User::find()->all();
		foreach ($users as $user) {
			if (!$user->subscribe) {
				$subscribe = Yii::createObject(UserSubscribe::class);
				$subscribe->link('user', $user);
			}
		}
	}

	public function down() {
		$this->dropTable('{{%user_subscribe}}');
	}
}
