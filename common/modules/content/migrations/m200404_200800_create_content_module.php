<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation for table `content_module`.
 */
class m200404_200800_create_content_module extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%content_module}}', [
			'id' => $this->primaryKey(),
			'content_id' => $this->integer()->notNull(),
			'module_type' => $this->integer()->notNull(),
			'module_id' => $this->integer()->notNull(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_module-content_id', '{{%content_module}}', ['content_id']);
		$this->createIndex('idx-content_module-module_type', '{{%content_module}}', ['module_type']);
		$this->createIndex('idx-content_module-module_id', '{{%content_module}}', ['module_id']);
		
		// create pk
		$this->addForeignKey('fk-content_module-content_id', '{{%content_module}}', 'content_id', '{{%content}}', 'id', 'CASCADE', 'RESTRICT');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk-content_module-content_id', '{{%content_module}}');
			
		// drop table
		$this->dropTable('{{%content_module}}');
	}
}
