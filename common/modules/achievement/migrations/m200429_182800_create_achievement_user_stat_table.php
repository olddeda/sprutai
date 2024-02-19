<?php
namespace common\modules\achievement\migrations;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use yii\db\Migration;

/**
 * Handles the creation for table `achievement_user`.
 */
class m200429_182800_create_achievement_user_stat_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%achievement_user_stat}}', [
            'id' => $this->primaryKey(),
			'type' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
            'count' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-achievement_user_stat-unique', '{{%achievement_user_stat}}', ['type', 'user_id']);

        $this->addForeignKey('fk-achievement_user_stat-user_id', '{{%achievement_user_stat}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropForeignKey('fk-achievement_user_stat-user_id', '{{%achievement_user_stat}}');

        $this->dropTable('{{%achievement_user_stat}}');
    }
}
