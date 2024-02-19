<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

class m180720_222204_add_text_new_field_to_content_table extends Migration
{
	public function up() {
		$this->addColumn('{{%content}}', 'text_new', $this->text()->null()->after('text'));
	}

	public function down() {
		$this->dropColumn('{{%content}}', 'text_new');
	}
}
