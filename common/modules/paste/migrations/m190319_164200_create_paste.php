<?php
namespace common\modules\paste\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `paste`.
 */
class m190319_164200_create_paste extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%paste}}', [
			'id' => $this->primaryKey(),
			'slug' => $this->string(8)->notNull(),
			'mode' => $this->string(100)->notNull(),
			'descr' => $this->string(255)->notNull(),
			'code' => $this->text(),
			'is_private' => $this->boolean()->defaultValue(false),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		]);

		// creates index for column `status`
		$this->createIndex('idx-paste-status', '{{%paste}}', ['status']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%paste}}');
	}
}
