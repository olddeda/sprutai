<?php
namespace common\modules\notification\migrations;

use yii\db\Migration;

/**
 * Class m170419_203853_create_table_notification
 * @package common\modules\notification\migrations
 */
class m170419_203853_create_table_notification extends Migration {
	
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->createTable('{{%notification}}', [
			'id' => $this->primaryKey(),
			'from_id' => $this->integer(11),
			'to_id' => $this->integer(11),
			'title' => $this->string(255),
			'message' => $this->text(),
			'params' => $this->text(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropTable('{{%notification}}');
	}
}
