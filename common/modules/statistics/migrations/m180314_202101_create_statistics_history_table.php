<?php
namespace common\modules\statistics\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `statistics_history`.
 */
class m180314_202101_create_statistics_history_table extends Migration
{
	/**
	 * @inheritdoc
	 */
    public function up() {
        $this->createTable('{{%statistics_history}}', [
            'id' => $this->primaryKey(),
	        'statistics_id' => $this->integer()->notNull(),
            'type' => $this->integer(2)->notNull(),
			'user_id' => $this->integer()->notNull(),
	        'user_ip' => $this->integer()->unsigned()->notNull(),
			'status' => $this->integer(2),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
        
		$this->createIndex('idx-statistics_history-statistics_id-status', '{{%statistics_history}}', ['statistics_id', 'status']);
		$this->createIndex('idx-statistics_history-statistics_id-type-status', '{{%statistics_history}}', ['statistics_id', 'type', 'status']);
	
		$this->addForeignKey('fk-statistics_history-statistics_id', '{{%statistics_history}}', 'statistics_id', '{{%statistics}}', 'id', 'CASCADE', 'CASCADE');
    }
	
	/**
	 * @inheritdoc
	 */
    public function down() {
    	$this->dropForeignKey('fk-statistics_history-statistics_id', '{{%statistics_history}}');
    	
        $this->dropTable('{{%statistics_history}}');
    }
}
