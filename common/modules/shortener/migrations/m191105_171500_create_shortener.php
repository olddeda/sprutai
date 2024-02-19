<?php
namespace common\modules\shortener\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `shortener`.
 */
class m191105_171500_create_shortener extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
	 
		// create table
		$this->createTable('{{%shortener}}', [
			'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'url' => $this->string(1000)->notNull(),
            'description' => $this->string(255),
            'hash' => $this->string()->notNull()->unique(),
            'counter' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->integer(1)->defaultValue(0),
			'created_by' => $this->integer()->notNull(),
			'updated_by' => $this->integer()->notNull(),
            'expiration_at' => $this->integer(11),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);
		
        $this->createTable('{{%shortener_hit}}', [
            'id' => $this->primaryKey(),
            'link_id' => $this->integer(11)->notNull(),
            'ip' => $this->string(50)->null(),
            'country' => $this->string()->null(),
            'city' => $this->string()->null(),
            'user_agent' => $this->string()->null(),
            'os' => $this->string()->null(),
            'os_version' => $this->string()->null(),
            'browser' => $this->string()->null(),
            'browser_version' => $this->string()->null(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
        
        $this->addForeignKey('fk_shortener_hit_id', '{{%shortener_hit}}', 'link_id', '{{%shortener}}', 'id', 'CASCADE', 'CASCADE');
        
        $this->createIndex('idx-shortener-status', '{{%shortener}}', ['status']);
        $this->createIndex('idx_shortener_hit_link_id', '{{%shortener_hit}}', 'link_id');
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
	    $this->dropForeignKey('fk_shortener_hit_id', '{{%shortener_hit}}');
	    
		$this->dropTable('{{%shortener}}');
        $this->dropTable('{{%shortener_hit}}');
	}
}
