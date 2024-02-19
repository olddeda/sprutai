<?php
namespace common\modules\telegram\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `telegram_chat`.
 */
class m200228_013400_add_description_field_to_telegram_chat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn('{{%telegram_chat}}', 'description', $this->text(1000)->null()->after('username'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn('{{%telegram_chat}}', 'description');
	}
}
