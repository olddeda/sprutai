<?php
namespace common\modules\content\migrations;

use yii\db\Command;
use yii\db\Migration;

use common\modules\vote\models\Vote;

use common\modules\content\models\ContentCompanyStat;
use common\modules\company\models\Company;

/**
 * Handles the creation for table `content_unique`.
 */
class m190414_182000_create_content_unique extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		// create table
		$this->createTable('{{%content_unique}}', [
			'id' => $this->primaryKey(),
			'content_id' => $this->integer()->notNull(),
			'text' => $this->text()->notNull(),
			'uid' => $this->string(100)->notNull(),
			'urls' => $this->json()->null(),
			'spellcheck' => $this->json()->null(),
			'unique' => $this->decimal(5,2)->null(),
			'count_chars_with_space' => $this->integer()->null(),
			'count_chars_without_space' => $this->integer()->null(),
			'count_words' => $this->integer()->null(),
			'water_percent' => $this->integer(3)->null(),
			'spam_percent' => $this->integer(3)->null(),
			'queue' => $this->integer(3)->null(),
			'status' => $this->integer(1)->defaultValue(0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_unique-content_id', '{{%content_unique}}', ['content_id']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// drop table
		$this->dropTable('{{%content_unique}}');
	}
}
