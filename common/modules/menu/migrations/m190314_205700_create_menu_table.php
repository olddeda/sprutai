<?php
namespace common\modules\menu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m190314_205700_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
	
		$this->createTable('{{%menu}}', [
			'id' => $this->primaryKey(),
			'tag_id' => $this->integer()->notNull(),
			'title' => $this->string(255)->notNull(),
			'sequence' => $this->integer()->defaultValue(0),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
	
		$this->createIndex('idx-menu_module-status', '{{%menu}}', ['status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%menu}}');
    }
}
