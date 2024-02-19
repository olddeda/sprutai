<?php

namespace common\modules\media\migrations;

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m151021_160500_init_media_format
 */
class m151021_160500_init_media_format extends Migration
{
    /**
     * Create tables
     */
    public function up() {

		$this->createTable('{{%media_format}}', [
			'id' => $this->primaryKey(),
			'width' => $this->integer(4)->notNull(),
			'height' => $this->integer(4)->notNull(),
			'mode' => $this->integer(1)->notNull(),
			'watermark' => $this->boolean()->notNull()->defaultValue(false),
			'format' => $this->string(20)->notNull()->unique(),
			'status' => $this->integer(1)->notNull(),
			'created_by' => $this->integer()->notNull()->defaultValue(0),
			'updated_by' => $this->integer()->notNull()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);

		//$this->addForeignKey('fk_media_format_created_by', '{{%media_format}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
		//$this->addForeignKey('fk_media_format_updated_by', '{{%media_format}}', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');

		$this->createIndex('idx_media_format_format_status', '{{%media_format}}', ['format', 'status']);
		$this->createIndex('idx_media_format_status', '{{%media}}', ['status']);
    }

    /**
     * Drop tables
     */
    public function down() {
        $this->dropTable('{{%media_format}}');
    }
}