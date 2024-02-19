<?php
namespace common\modules\tag\migrations;

use yii\db\Migration;

class m190203_212800_add_fields_to_tag_table extends Migration
{
	public function up() {
		$this->addColumn('{{%tag}}', 'descr', $this->text(10000)->after('title'));
		$this->addColumn('{{%tag}}', 'text', $this->text(100000)->after('descr'));
	}

	public function down() {
		$this->dropColumn('{{%tag}}', 'descr');
		$this->dropColumn('{{%tag}}', 'text');
	}
}