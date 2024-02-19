<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

class m181002_215154_add_pinned_field_to_content_table extends Migration
{
	public function up() {
		$this->addColumn('{{%content}}', 'pinned', $this->boolean()->defaultValue(false)->after('page_path'));
		$this->addColumn('{{%content}}', 'pinned_sequence', $this->integer()->defaultValue(0)->after('pinned'));
	}

	public function down() {
		$this->dropColumn('{{%content}}', 'pinned');
		$this->dropColumn('{{%content}}', 'pinned_sequence');
	}
}
