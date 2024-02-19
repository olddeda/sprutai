<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `tag_nested`.
 */
class m171128_103233_create_tag_nested_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%tag_nested}}', [
            'id' => $this->primaryKey(),
			'parent_id' => $this->integer()->defaultValue(0),
			'tag_id' => $this->integer()->notNull(),
			'root' => $this->integer()->unsigned()->null(),
			'lft' => $this->integer()->unsigned()->notNull(),
			'rgt' => $this->integer()->unsigned()->notNull(),
			'depth' => $this->integer()->unsigned()->notNull(),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
        
        $this->createIndex('idx-tag_nested-root-lft-rgt', '{{%tag_nested}}', ['root', 'lft', 'rgt']);
		$this->createIndex('idx-tag_nested-root-rgt', '{{%tag_nested}}', ['root', 'rgt']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%tag_nested}}');
    }
}
