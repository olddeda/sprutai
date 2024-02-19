<?php
namespace common\modules\vote\migrations;

class m160706_223457_vote_index extends Migration {
	
    public function up() {
        $this->createIndex('idx-vote-entity_value', '{{%vote}}', ['entity', 'entity_id', 'value'], false);
    }

    public function down() {
        $this->dropIndex('idx-vote-entity_value', '{{%vote}}');
    }
}
