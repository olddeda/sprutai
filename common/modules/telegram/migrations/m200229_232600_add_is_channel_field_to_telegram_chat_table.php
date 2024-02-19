<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `telegram_chat`.
 */
class m200229_232600_add_is_channel_field_to_telegram_chat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn('{{%telegram_chat}}', 'is_channel', $this->boolean()->defaultValue(false)->after('is_partner'));
        $this->addColumn('{{%telegram_chat}}', 'is_spam_protect', $this->boolean()->defaultValue(false)->after('is_channel'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn('{{%telegram_chat}}', 'is_channel');
	}
}
