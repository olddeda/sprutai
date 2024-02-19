<?php
namespace common\modules\dashboard\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dashboard`.
 */
class m170414_150708_create_dashboard_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%dashboard}}', [
            'id' => $this->primaryKey(),
	        'user_id' => $this->integer()->notNull(),
	        'name' => $this->string(255)->notNull(),
	        'width' => $this->integer(2)->notNull(),
	        'height' => $this->integer(2)->notNull(),
	        'x' => $this->integer(2)->notNull(),
	        'y' => $this->integer(2)->notNull(),
	        'params' => $this->text()->null(),
	        'status' => $this->integer(1)->defaultValue(0),
	        'created_by' => $this->integer()->notNull(),
	        'updated_by' => $this->integer()->notNull(),
	        'created_at' => $this->integer(11),
	        'updated_at' => $this->integer(11),
        ]);
	
	    // creates index for column `status` and `type`
	    $this->createIndex('idx-dashboard-user_id', '{{%dashboard}}', ['user_id']);
	    $this->createIndex('idx-dashboard-user_id-status', '{{%dashboard}}', ['user_id', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%dashboard}}');
    }
}
