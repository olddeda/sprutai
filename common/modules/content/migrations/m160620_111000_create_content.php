<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `content`.
 */
class m160620_111000_create_content extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%content}}', [
			'id' => $this->primaryKey(),
			'module_type' => $this->integer()->defaultValue(0),
			'module_id' => $this->integer()->defaultValue(0),
			'content_id' => $this->integer()->defaultValue(0),
			'author_id' => $this->integer()->notNull(),
			'type' => $this->integer(2)->defaultValue(0),
			'title' => $this->string(255)->notNull(),
			'descr' => $this->text(),
			'text' => $this->text(),
			'source_name' => $this->string(255),
			'source_url' => $this->string(255),
			'layout' => $this->string(255)->defaultValue(''),
			'is_main' => $this->integer(1)->defaultValue(0),
			'page_type' => $this->integer()->defaultValue(0),
			'page_path' => $this->string(255)->null(),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'date_at' => $this->integer(11),
			'published_at' => $this->integer(11)->defaultValue(null),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates index for column `status` and `type`
		$this->createIndex('idx-content-status-type', '{{%content}}', ['status', 'type']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%content}}');
	}
}
