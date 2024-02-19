<?php

use yii\db\Migration;
use yii\db\Schema;

class m150623_212711_fix_username_notnull extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%user}}', 'username', Schema::TYPE_STRING.'(255) NOT NULL');
    }

    public function down()
    {
        $this->alterColumn('{{%user}}', 'username', Schema::TYPE_STRING.'(255)');
    }
}
