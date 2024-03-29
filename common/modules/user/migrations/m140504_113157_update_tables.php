<?php

use common\modules\user\migrations\Migration;
use yii\db\Schema;

class m140504_113157_update_tables extends Migration
{
    public function up() {

        // User table
        $this->dropIndex('user_confirmation', '{{%user}}');
        $this->dropIndex('user_recovery', '{{%user}}');
        $this->dropColumn('{{%user}}', 'confirmation_token');
        $this->dropColumn('{{%user}}', 'confirmation_sent_at');
        $this->dropColumn('{{%user}}', 'recovery_token');
        $this->dropColumn('{{%user}}', 'recovery_sent_at');
        $this->dropColumn('{{%user}}', 'logged_in_from');
        $this->dropColumn('{{%user}}', 'logged_in_at');
        $this->renameColumn('{{%user}}', 'registered_from', 'registration_ip');
        $this->addColumn('{{%user}}', 'flags', Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0');

        // User account table
        $this->renameColumn('{{%user_account}}', 'properties', 'data');
    }

    public function down() {

        // User account table
        $this->renameColumn('{{%user_account}}', 'data', 'properties');

        // User table
        $this->dropColumn('{{%user}}', 'flags');
        $this->renameColumn('{{%user}}', 'registration_ip', 'registered_from');
        $this->addColumn('{{%user}}', 'logged_in_at', Schema::TYPE_INTEGER);
        $this->addColumn('{{%user}}', 'logged_in_from', Schema::TYPE_INTEGER);
        $this->addColumn('{{%user}}', 'recovery_sent_at', Schema::TYPE_INTEGER);
        $this->addColumn('{{%user}}', 'recovery_token', Schema::TYPE_STRING . '(32)');
        $this->addColumn('{{%user}}', 'confirmation_sent_at', Schema::TYPE_INTEGER);
        $this->addColumn('{{%user}}', 'confirmation_token', Schema::TYPE_STRING . '(32)');
        $this->createIndex('user_confirmation', '{{%user}}', 'id, confirmation_token', true);
        $this->createIndex('user_recovery', '{{%user}}', 'id, recovery_token', true);
    }
}
