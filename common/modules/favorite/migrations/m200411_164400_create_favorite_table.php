<?php
namespace common\modules\favorite\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `favorite`.
 */
class m200411_164400_create_favorite_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {

        $this->createTable('{{%favorite}}', [
            'id' => $this->primaryKey(),
			'group_id' => $this->integer()->notNull(),
			'module_type' => $this->integer()->notNull(),
			'module_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-favorite-module_type-module_id', '{{%favorite}}', ['module_type', 'module_id']);
        $this->createIndex('idx-favorite-module_type-user_id', '{{%favorite}}', ['module_type', 'user_id']);
        $this->createIndex('idx-favorite-user_id', '{{%favorite}}', ['user_id']);

        $this->addForeignKey('fk-favorite-group', '{{%favorite}}', 'group_id', '{{%favorite_group}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-favorite-user', '{{%favorite}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropForeignKey('fk-favorite-group', '{{%favorite}}');
        $this->dropForeignKey('fk-favorite-user', '{{%favorite}}');

        $this->dropTable('{{%favorite}}');
    }
}
