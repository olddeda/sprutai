<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `tag_module`.
 */
class m171128_100913_create_tag_module_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
		$this->createTable('{{%tag_module}}', [
			'id' => $this->primaryKey(),
			'tag_id' => $this->integer()->notNull(),
			'module_type' => $this->integer()->notNull(),
			'module_id' => $this->integer()->notNull(),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
	
		$this->createIndex('idx-tag_module-module_type-module_id-status', '{{%tag_module}}', ['module_type', 'module_id', 'status']);
		$this->createIndex('idx-tag_module-module_type-status', '{{%tag_module}}', ['module_type', 'status']);
		$this->createIndex('idx-tag_module-tag_id-status', '{{%tag_module}}', ['tag_id', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%tag_module}}');
    }
}
