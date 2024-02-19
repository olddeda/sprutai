<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

class m180525_161004_add_video_url_field_to_content_table extends Migration
{
	public function up() {
		$this->addColumn('{{%content}}', 'video_url', $this->string()->null()->after('source_url'));
	}

	public function down() {
		$this->dropColumn('{{%content}}', 'video_url');
	}
}
