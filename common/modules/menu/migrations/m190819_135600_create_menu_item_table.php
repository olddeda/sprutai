<?php
namespace common\modules\menu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `menu_item`.
 */
class m190819_135600_create_menu_item_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
	
		$this->createTable('{{%menu_item}}', [
			'id' => $this->primaryKey(),
			'menu_id' => $this->integer()->notNull(),
			'title' => $this->string(255)->null(),
			'descr' => $this->text()->null(),
			'url' => $this->string(255)->null(),
			'sequence' => $this->integer()->defaultValue(0),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
	
		$this->createIndex('idx-menu_item-status', '{{%menu_item}}', ['status']);
		$this->createIndex('idx-menu_item-menu_id-status', '{{%menu_item}}', ['menu_id', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%menu_item}}');
    }
}
