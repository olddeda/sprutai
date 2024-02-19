<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `telegram_chat`.
 */
class m190228_223900_add_username_field_to_telegram_chat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn('{{%telegram_chat}}', 'username', $this->string(255)->notNull()->after('title'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn('{{%telegram_chat}}', 'username');
	}
}
