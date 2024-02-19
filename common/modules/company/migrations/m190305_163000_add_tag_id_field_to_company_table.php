<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

class m190305_163000_add_tag_id_field_to_company_table extends Migration
{
	public function up() {
		$this->addColumn('{{%company}}', 'tag_id', $this->integer()->after('type'));
	}

	public function down() {
		$this->dropColumn('{{%company}}', 'tag_id');
	}
}
