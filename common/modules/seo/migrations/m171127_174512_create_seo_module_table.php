<?php
namespace common\modules\seo\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `seo_module`.
 */
class m171127_174512_create_seo_module_table extends Migration
{
	/**
	 * @inheritdoc
	 */
    public function up() {
        $this->createTable('{{%seo_module}}', [
            'id' => $this->primaryKey(),
	        'module_type' => $this->integer()->notNull(),
			'module_class' => $this->string(255)->notNull(),
	        'slugify' => $this->string(255)->notNull(),
			'status' => $this->integer(2),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
	
		$this->createIndex('idx-seo-module-module_type-status', '{{%seo_module}}', ['module_type', 'status']);
    }
	
	/**
	 * @inheritdoc
	 */
    public function down() {
        $this->dropTable('{{%seo_module}}');
    }
}
