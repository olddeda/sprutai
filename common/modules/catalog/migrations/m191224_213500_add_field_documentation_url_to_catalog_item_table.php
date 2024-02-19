<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m191224_213500_add_field_documentation_url_to_catalog_item_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item}}', 'documentation_url', $this->string(255)->null()->after('url'));
	}

	public function down() {
		$this->dropColumn('{{%catalog)item}}', 'documentation_url');
	}
}