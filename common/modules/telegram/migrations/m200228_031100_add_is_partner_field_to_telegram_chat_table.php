<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `telegram_chat`.
 */
class m200228_031100_add_is_partner_field_to_telegram_chat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn('{{%telegram_chat}}', 'is_partner', $this->boolean()->defaultValue(true)->after('notify_payment'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn('{{%telegram_chat}}', 'is_partner');
	}
}
