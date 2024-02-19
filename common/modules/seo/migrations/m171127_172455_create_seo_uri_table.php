<?php
namespace common\modules\seo\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `seo_uri`.
 */
class m171127_172455_create_seo_uri_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%seo_uri}}', [
            'id' => $this->primaryKey(),
			'module_type' => $this->integer()->notNull(),
			'module_id' => $this->integer()->notNull(),
			'module_route' => $this->string(255)->notNull(),
			'module_params' => $this->string(4000),
			'uri' => $this->string(1000),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
	
		$this->createIndex('idx-seo-uri-module_id-module_route', '{{%seo_uri}}', ['module_id', 'module_route']);
		$this->createIndex('idx-seo-uri-uri', '{{%seo_uri}}', ['uri']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%seo_uri}}');
    }
}
