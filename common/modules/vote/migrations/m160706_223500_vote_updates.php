<?php
namespace common\modules\vote\migrations;

class m160706_223500_vote_updates extends Migration
{
    public function up() {
        $this->createIndex('idx-vote-entity_user', '{{%vote}}', ['entity', 'entity_id', 'user_id'], false);
        $this->alterColumn('{{%vote}}', 'value', $this->boolean()->notNull());

        //TODO: check these command and fix migration
        //$this->execute('ALTER TABLE vote ALTER COLUMN value TYPE boolean USING CASE value WHEN 0 THEN false ELSE true END');
    }

    public function down() {
        $this->dropIndex('idx-vote-entity_user', '{{%vote}}');
        $this->alterColumn('{{%vote}}', 'value', $this->smallInteger(1)->notNull());
    }
}
