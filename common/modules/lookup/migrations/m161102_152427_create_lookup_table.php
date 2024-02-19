<?php
namespace common\modules\lookup\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `lookup`.
 */
class m161102_152427_create_lookup_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('{{%lookup}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->defaultValue(0),
            'code' => $this->integer(2)->notNull(),
            'type' => $this->integer(2)->notNull(),
            'sequence' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'text' => $this->text(),
            'foreign' => $this->boolean(),
			'status' => $this->integer(1),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);
	
		// creates index for column `status`
		$this->createIndex('idx-lookup-status', '{{%lookup}}', 'status');
	
		// creates index for column `parent_id, status`
		$this->createIndex('idx-lookup-parent_id-status', '{{%lookup}}', ['parent_id', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {

		// drop table
        $this->dropTable('{{%lookup}}');
    }
}
