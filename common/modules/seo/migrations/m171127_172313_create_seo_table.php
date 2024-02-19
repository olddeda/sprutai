<?php
namespace common\modules\seo\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `seo`.
 */
class m171127_172313_create_seo_table extends Migration
{
	/**
	 * @inheritdoc
	 */
    public function up() {
        $this->createTable('{{%seo}}', [
            'id' => $this->primaryKey(),
	        'module_class' => $this->string(255)->notNull(),
	        'module_name' => $this->string(150)->notNull(),
	        'module_type' => $this->integer()->notNull(),
            'module_id' => $this->integer()->notNull(),
	        'slugify' => $this->string(255)->null(),
            'h1' => $this->string(255)->null(),
            'title' => $this->string(255)->null(),
            'keywords' => $this->string(255)->null(),
            'description' => $this->string(522)->null(),
            'text' => $this->text()->null(),
			'status' => $this->integer(2),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
	
		$this->createIndex('idx-seo-module_name-module_id', '{{%seo}}', ['module_name', 'module_id']);
    }
	
	/**
	 * @inheritdoc
	 */
    public function down() {
        $this->dropTable('{{%seo}}');
    }
}
