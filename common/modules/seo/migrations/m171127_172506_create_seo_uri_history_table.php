<?php
namespace common\modules\seo\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `seo_uri_history`.
 */
class m171127_172506_create_seo_uri_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%seo_uri_history}}', [
            'id' => $this->primaryKey(),
			'seo_uri_id' => $this->integer()->notNull(),
			'uri' => $this->string(1000),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
	
		$this->addForeignKey('fk-seo_uri_history-seo_uri_id', '{{%seo_uri_history}}', 'seo_uri_id', '{{%seo_uri}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down() {
    	$this->dropForeignKey('fk-seo_uri_history-seo_uri_id', '{{%seo_uri_history}}');
        $this->dropTable('{{%seo_uri_history}');
    }
}
