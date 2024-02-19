<?php
namespace common\modules\menu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `menu_nested`.
 */
class m190819_135700_create_menu_nested_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%menu_nested}}', [
            'id' => $this->primaryKey(),
			'menu_id' => $this->integer()->notNull(),
			'menu_item_id' => $this->integer()->notNull(),
			'parent_id' => $this->integer()->defaultValue(0),
			'root' => $this->integer()->unsigned()->null(),
			'lft' => $this->integer()->unsigned()->notNull(),
			'rgt' => $this->integer()->unsigned()->notNull(),
			'depth' => $this->integer()->unsigned()->notNull(),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
        
        $this->createIndex('idx-menu_nested-root-lft-rgt', '{{%menu_nested}}', ['root', 'lft', 'rgt']);
		$this->createIndex('idx-menu_nested-root-rgt', '{{%menu_nested}}', ['root', 'rgt']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%menu_nested}}');
    }
}
