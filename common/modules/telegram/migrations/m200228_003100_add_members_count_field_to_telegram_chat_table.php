<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `telegram_chat`.
 */
class m200228_003100_add_members_count_field_to_telegram_chat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn('{{%telegram_chat}}', 'members_count', $this->integer()->defaultValue(0)->after('notify_payment'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn('{{%telegram_chat}}', 'members_count');
	}
}
