<?php
namespace common\modules\settings\migrations;

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m150227_114524_init
 * Init settings table
 */
class m000000_000000_init extends Migration
{
    /**
     * This method contains the logic to be executed when applying this migration.
     */
    public function up() {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(10)->notNull(),
            'section' => $this->string(255)->notNull(),
            'key' => $this->string(255)->notNull(),
            'value' => $this->string(255)->notNull(),
			'descr' => $this->string(255)->notNull(),
			'status' => $this->integer(1)->notNull(),
			'created_by' => $this->integer()->notNull()->defaultValue(0),
			'updated_by' => $this->integer()->notNull()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     */
    public function down() {
        $this->dropTable('{{%settings}}');
    }
}
