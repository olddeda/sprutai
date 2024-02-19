<?php

use yii\db\Schema;
use yii\db\Migration;

class m181224_013522_add_fields_to_user_subscribe_table extends Migration
{
	public function up() {
		$this->addColumn('{{%user_subscribe}}', 'flag_news', $this->integer(2)->defaultValue(1)->after('flag_article'));
		$this->addColumn('{{%user_subscribe}}', 'flag_project', $this->integer(2)->defaultValue(1)->after('flag_news'));
		$this->addColumn('{{%user_subscribe}}', 'flag_blog', $this->integer(2)->defaultValue(1)->after('flag_project'));
		$this->addColumn('{{%user_subscribe}}', 'flag_plugin', $this->integer(2)->defaultValue(1)->after('flag_blog'));
	}

	public function down() {
		$this->dropColumn('{{%user_profile}}', 'flag_news');
		$this->dropColumn('{{%user_profile}}', 'flag_project');
		$this->dropColumn('{{%user_profile}}', 'flag_blog');
		$this->dropColumn('{{%user_profile}}', 'flag_plugin');
	}
}
