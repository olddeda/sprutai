<?php
namespace common\modules\content\migrations;

use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation for table `content_tag`.
 */
class m190128_023600_create_content_tag extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%content_tag}}', [
			'id' => $this->primaryKey(),
			'content_id' => $this->integer()->notNull(),
			'tag_id' => $this->integer()->notNull(),
			'author_id' => $this->integer()->notNull(),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_tag->content_id', '{{%content_tag}}', ['content_id']);
		$this->createIndex('idx-content_tag->tag_id', '{{%content_tag}}', ['tag_id']);
		$this->createIndex('idx-content_tag->author_id', '{{%content_tag}}', ['author_id']);
		
		// create pk
		$this->addForeignKey('fk_content_tag_content_id', '{{%content_tag}}', 'content_id', '{{%content}}', 'id', 'CASCADE', 'RESTRICT');
		$this->addForeignKey('fk_content_tag_author_id', '{{%content_tag}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
		$this->addForeignKey('fk_content_tag_tag_id', '{{%content_tag}}', 'tag_id', '{{%tag}}', 'id', 'CASCADE', 'RESTRICT');
		
		$contents = Content::find()->where([])->all();
		
		$tags = [];
		foreach ($contents as $content) {
			if (count($content->getTags_ids())) {
				$tags[] = [
					'content_id' => $content->id,
					'author_id' => $content->author_id,
					'tags_ids' => $content->getTags_ids(),
				];
			}
		}
		
		if (count($tags)) {
			foreach ($tags as $t) {
				ContentTag::updateLinks($t['content_id'], $t['author_id'], $t['tags_ids']);
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk_content_tag_content_id', '{{%content_tag}}');
		$this->dropForeignKey('fk_content_tag_author_id', '{{%content_tag}}');
		$this->dropForeignKey('fk_content_tag_tag_id', '{{%content_tag}}');
			
		// drop table
		$this->dropTable('{{%content_tag}}');
	}
}
