<?php
namespace common\modules\hub\migrations;

use yii\db\Migration;

use common\modules\achievement\helpers\enum\Type;

/**
 * Handles the creation for table `hub_license`.
 */
class m200608_003700_create_hub_license_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->createTable('{{%hub_license}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull(),
            'user_id' => $this->integer()->null(),
			'code' => $this->string(255)->notNull(),
            'status' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-hub_license-type', '{{%hub_license}}', ['type']);
        $this->createIndex('idx-hub_license-type-user_id', '{{%hub_license}}', ['type', 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropTable('{{%hub_license}}');
    }
}
