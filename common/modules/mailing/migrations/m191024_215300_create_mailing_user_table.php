<?php
namespace common\modules\mailing\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `mailing_user`.
 */
class m191024_215300_create_mailing_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up() {
	
		$this->createTable('{{%mailing_user}}', [
			'id' => $this->primaryKey(),
			'type' => $this->integer()->defaultValue(0),
			'email' => $this->string(255)->notNull(),
			'status' => $this->integer(2)->defaultValue(0),
			'created_by' => $this->integer()->defaultValue(0),
			'updated_by' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);
	
		$this->createIndex('idx-mailing_user_type-status', '{{%mailing_user}}', ['type', 'status']);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('{{%mailing_user}}');
    }
}
