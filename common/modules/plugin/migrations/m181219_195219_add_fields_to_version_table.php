<?php
namespace common\modules\plugin\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m181219_195219_add_fields_to_version_table extends Migration
{
	public function up() {
		$this->addColumn('{{%plugin_version}}', 'data', $this->text()->defaultValue(null)->after('text'));
		$this->addColumn('{{%plugin_version}}', 'latest', $this->boolean()->defaultValue(false)->after('data'));
	}

	public function down() {
		$this->dropColumn('{{%plugin_version}}', 'data');
		$this->dropColumn('{{%plugin_version}}', 'latest');
	}
}
