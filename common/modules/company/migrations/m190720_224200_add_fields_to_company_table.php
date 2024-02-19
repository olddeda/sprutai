<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

class m190720_224200_add_fields_to_company_table extends Migration
{
	public function up() {
		$this->addColumn('{{%company}}', 'telegram', $this->string(100)->defaultValue(null)->after('phone'));
		$this->addColumn('{{%company}}', 'instagram', $this->string(100)->defaultValue(null)->after('telegram'));
		$this->addColumn('{{%company}}', 'facebook', $this->string(100)->defaultValue(null)->after('instagram'));
		$this->addColumn('{{%company}}', 'vk', $this->string(100)->defaultValue(null)->after('facebook'));
		$this->addColumn('{{%company}}', 'ok', $this->string(100)->defaultValue(null)->after('vk'));
	}

	public function down() {
		$this->dropColumn('{{%company}}', 'telegram');
		$this->dropColumn('{{%company}}', 'instagram');
		$this->dropColumn('{{%company}}', 'facebook');
		$this->dropColumn('{{%company}}', 'ok');
		$this->dropColumn('{{%company}}', 'vk');
	}
}
