<?php
namespace common\modules\vote\migrations;

class m160620_131811_vote extends Migration
{
    public function up() {
        $this->createTable('{{%vote}}', [
            'id' => $this->primaryKey(),
            'entity' => $this->integer()->unsigned()->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'user_ip' => $this->string(39)->notNull()->defaultValue('127.0.0.1'),
            'value' => $this->smallInteger(1)->notNull(),
            'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
        
        $this->createTable('{{%vote_aggregate}}', [
            'id' => $this->primaryKey(),
            'entity' => $this->integer()->unsigned()->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'positive' => $this->integer()->defaultValue(0),
            'negative' => $this->integer()->defaultValue(0),
            'rating' => $this->float()->unsigned()->notNull()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
        ]);
        
        $this->createIndex('idx-vote-entity', '{{%vote}}', ['entity', 'entity_id'], false);
        $this->createIndex('idx-vote-user', '{{%vote}}', 'user_id', false);
        $this->createIndex('idx-vote-user_ip', '{{%vote}}', 'user_ip', false);
        $this->createIndex('idx-vote_aggregate-entity', '{{%vote_aggregate}}', ['entity', 'entity_id'], true);
    }

    public function down() {
        $this->dropTable('{{%vote}}');
        $this->dropTable('{{%vote_aggregate}}');
    }
}
