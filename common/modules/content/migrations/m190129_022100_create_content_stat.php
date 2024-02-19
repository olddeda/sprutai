<?php
namespace common\modules\content\migrations;

use common\modules\base\components\Debug;
use common\modules\content\helpers\enum\Status;
use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentStat;

/**
 * Handles the creation for table `content_stat`.
 */
class m190129_022100_create_content_stat extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		// create table
		$this->createTable('{{%content_stat}}', [
			'id' => $this->primaryKey(),
			'content_id' => $this->integer()->notNull(),
			'comments' => $this->integer()->defaultValue(0),
			'likes' => $this->integer()->defaultValue(0),
			'favorites' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_stat->content_id', '{{%content_stat}}', ['content_id']);
		
		// create pk
		$this->addForeignKey('fk_content_stat_content_id', '{{%content_stat}}', 'content_id', '{{%content}}', 'id', 'CASCADE', 'RESTRICT');
		
		$models = Content::find()
			->joinWith(['comments'])
			->votes()
			->where(['not in', Content::tableName().'.status', [Status::TEMP, Status::DELETED]])
			->all();
		
		foreach ($models as $model) {
			ContentStat::updateLinks($model);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk_content_stat_content_id', '{{%content_stat}}');
			
		// drop table
		$this->dropTable('{{%content_stat}}');
	}
}
