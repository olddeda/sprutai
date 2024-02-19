<?php

namespace common\modules\media\migrations;

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m151017_211400_init_media_format
 */
class m151021_160300_init_media extends Migration
{
	/**
	 * Create tables
	 */
	public function up() {

		$this->createTable('{{%media}}', [
			'id' => $this->primaryKey(),
			'module_type' => $this->integer(11)->notNull(),
			'module_id' => $this->integer(11)->notNull(),
			'attribute' => $this->integer(255)->notNull(),
			'type' => $this->integer(1)->notNull()->defaultValue(0),
			'title' => $this->string(255)->defaultValue(''),
			'alt' => $this->string(255)->defaultValue(''),
			'descr' => $this->text(1000),
			'ext' => $this->string(5)->defaultValue(''),
			'is_main' => $this->boolean()->defaultValue(false),
			'width' => $this->integer(4)->defaultValue(0),
			'height' => $this->integer(4)->defaultValue(0),
			'size' => $this->integer()->notNull(),
			'sequence' => $this->integer()->defaultValue(0),
			'status' => $this->integer(1)->notNull(),
			'data' => 'LONGBLOB NOT NULL',
			'created_by' => $this->integer()->notNull()->defaultValue(0),
			'updated_by' => $this->integer()->notNull()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
		
		$this->createIndex('idx_media_module_type_module_id_is_main_status', '{{%media}}', ['module_type', 'module_id', 'is_main', 'status']);
		$this->createIndex('idx_media_status', '{{%media}}', ['status']);
	}

	/**
	 * Drop tables
	 */
	public function down() {
		$this->dropIndex('idx_media_module_type_module_id_status', '{{%media}}');
		$this->dropIndex('idx_media_status', '{{%media}}');
		
		$this->dropTable('{{%media}}');
	}
}