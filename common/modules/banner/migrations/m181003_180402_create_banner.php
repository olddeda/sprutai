<?php
namespace common\modules\banner\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `banner`.
 */
class m181003_180402_create_banner extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%banner}}', [
			'id' => $this->primaryKey(),
			'title' => $this->string(255)->notNull(),
			'url' => $this->string(255),
			'views' => $this->integer(11)->defaultValue(0),
			'visits' => $this->integer(11)->defaultValue(0),
			'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
			'date_from_at' => $this->integer(11),
			'date_to_at' => $this->integer(11),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates index for column `status`
		$this->createIndex('idx-banner-status', '{{%banner}}', ['status']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%banner}}');
	}
}
