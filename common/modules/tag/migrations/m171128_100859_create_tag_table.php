<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `tag`.
 */
class m171128_100859_create_tag_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
	
		$this->createTable('{{%tag}}', [
			'id' => $this->primaryKey(),
			'type' => $this->integer(2)->defaultValue(0),
			'title' => $this->string(255)->notNull(),
			'sequence' => $this->integer()->defaultValue(0),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
	
		$this->createIndex('idx-tag_module-status', '{{%tag}}', ['status']);
		$this->createIndex('idx-tag-type-status', '{{%tag}}', ['type', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%tag}}');
    }
}
