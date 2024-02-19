<?php
namespace common\modules\plugin\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `plugin_version`.
 */
class m181123_215213_create_plugin_version_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		// create table
		$this->createTable('{{%plugin_version}}', [
			'id' => $this->primaryKey(),
			'plugin_id' => $this->integer()->notNull(),
			'version' => $this->string()->notNull(),
			'url' => $this->string()->defaultValue(null),
			'text' => $this->text()->notNull(),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
            'date_at' => $this->integer()->null(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		]);
		
		// create index
		$this->createIndex('idx-plugin_version_plugin_id-status', '{{%plugin_version}}', ['plugin_id', 'status']);
		
		// create foreign keys
		$this->addForeignKey('fk-plugin_version-plugin', '{{%plugin_version}}', 'plugin_id', '{{%content}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk-plugin_version-created_by', '{{%plugin_version}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
		$this->addForeignKey('fk-plugin_version-updated_by', '{{%plugin_version}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// drop foreign keys
		$this->dropForeignKey('fk-plugin_version-plugin', '{{%plugin_version}}');
		$this->dropForeignKey('fk-plugin_version-created_by', '{{%plugin_version}}');
		$this->dropForeignKey('fk-plugin_version-updated_by', '{{%plugin_version}}');
		
		// drop index
		$this->dropIndex('idx-plugin_version_plugin_id-status', '{{%plugin_version}}');

		// drop table
		$this->dropTable('{{%plugin_version}}');
	}
}
