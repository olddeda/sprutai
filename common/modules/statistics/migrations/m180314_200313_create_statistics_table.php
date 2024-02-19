<?php
namespace common\modules\statistics\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `statistics`.
 */
class m180314_200313_create_statistics_table extends Migration
{
	/**
	 * @inheritdoc
	 */
    public function up() {
        $this->createTable('{{%statistics}}', [
            'id' => $this->primaryKey(),
	        'module_type' => $this->integer()->notNull(),
            'module_id' => $this->integer()->notNull(),
			'show' => $this->integer()->notNull(),
			'visit' => $this->integer()->notNull(),
			'outgoing' => $this->integer()->notNull(),
			'status' => $this->integer(2),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
        
		$this->createIndex('idx-statistics-module_name-module_id-status', '{{%statistics}}', ['module_type', 'module_id', 'status'], true);
    }
	
	/**
	 * @inheritdoc
	 */
    public function down() {
        $this->dropTable('{{%statistics}}');
    }
}
