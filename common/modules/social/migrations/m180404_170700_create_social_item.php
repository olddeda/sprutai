<?php
namespace common\modules\social\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `social_item`.
 */
class m180404_170700_create_social_item extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%social_item}}', [
			'id' => $this->primaryKey(),
			'module_type' => $this->integer()->defaultValue(0),
			'module_id' => $this->integer()->defaultValue(0),
			'post_telegram_at' => $this->integer(11),
			'post_facebook_at' => $this->integer(11),
			'post_instargam_at' => $this->integer(11),
			'post_vk_at' => $this->integer(11),
			'post_ok_at' => $this->integer(11),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%social_item}}');
	}
}
