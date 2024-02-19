<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

class m190415_003200_add_notification_field_to_content_table extends Migration
{
	public function up() {
		$this->addColumn('{{%content}}', 'notification', $this->boolean()->defaultValue(true)->after('pinned_sequence'));
        $this->addColumn('{{%content}}', 'change_catalog_links', $this->boolean()->defaultValue(false)->after('notification'));
	}

	public function down() {
		$this->dropColumn('{{%content}}', 'notification');
        $this->dropColumn('{{%content}}', 'change_catalog_links');
	}
}
