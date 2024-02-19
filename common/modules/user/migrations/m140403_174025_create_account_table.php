<?php

use common\modules\user\migrations\Migration;
use yii\db\Schema;

class m140403_174025_create_account_table extends Migration
{
    public function up() {
        $this->createTable('{{%user_account}}', [
            'id'         => Schema::TYPE_PK,
            'user_id'    => Schema::TYPE_INTEGER,
            'client_id'  => Schema::TYPE_STRING.' NOT NULL',
			'provider'   => Schema::TYPE_STRING.' NOT NULL',
            'properties' => Schema::TYPE_TEXT,
        ], $this->tableOptions);

        $this->createIndex('account_unique', '{{%user_account}}', ['provider', 'client_id'], true);

        $this->addForeignKey('fk_user_account', '{{%user_account}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down() {
        $this->dropTable('{{%user_account}}');
    }
}
