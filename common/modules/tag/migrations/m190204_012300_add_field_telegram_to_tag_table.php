<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

class m190204_012300_add_field_telegram_to_tag_table extends Migration
{
	public function up() {
		$this->addColumn('{{%tag}}', 'telegram', $this->string(255)->after('text'));
	}

	public function down() {
		$this->dropColumn('{{%tag}}', 'telegram');
	}
}