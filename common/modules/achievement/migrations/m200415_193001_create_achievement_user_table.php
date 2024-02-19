<?php
namespace common\modules\achievement\migrations;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use yii\db\Migration;

/**
 * Handles the creation for table `achievement_user`.
 */
class m200415_193001_create_achievement_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%achievement_user}}', [
            'id' => $this->primaryKey(),
			'achievement_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->null(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-achievement-unique', '{{%achievement_user}}', ['achievement_id', 'user_id']);

        $this->addForeignKey('fk-achievement_achievement', '{{%achievement_user}}', 'achievement_id', '{{%achievement}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-achievement_user', '{{%achievement_user}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropForeignKey('fk-achievement_achievement', '{{%achievement_user}}');
        $this->dropForeignKey('fk-achievement_user', '{{%achievement_user}}');

        $this->dropTable('{{%achievement_user}}');
    }
}
