<?php
namespace common\modules\favorite\migrations;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use yii\db\Migration;

/**
 * Handles the creation for table `favorite_group`.
 */
class m200411_164300_create_favorite_group_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {

        $this->createTable('{{%favorite_group}}', [
            'id' => $this->primaryKey(),
			'module_type' => $this->integer()->notNull(),
			'title' => $this->string(255)->notNull(),
            'sequence' => $this->integer()->defaultValue(0),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-favorite_group-user_id-module_type', '{{%favorite_group}}', ['user_id', 'module_type']);

        $this->addForeignKey('fk-favorite_group-user', '{{%favorite_group}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->insert('{{%favorite_group}}', [
            'title' => 'Избранное',
            'module_type' => ModuleType::CONTENT,
            'sequence' => -1,
            'user_id' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%favorite_group}}', [
            'title' => 'Избранное',
            'module_type' => ModuleType::CATALOG_ITEM,
            'sequence' => -1,
            'user_id' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%favorite_group}}', [
            'title' => 'Покупки',
            'module_type' => ModuleType::CATALOG_ITEM,
            'sequence' => -1,
            'user_id' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%favorite_group}}');
    }
}
