<?php
namespace common\modules\content\migrations;

use common\modules\content\models\ContentAuthorStat;
use common\modules\user\models\User;
use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation for table `content_author_stat`.
 */
class m190128_043100_create_content_author_stat extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		// create table
		$this->createTable('{{%content_author_stat}}', [
			'id' => $this->primaryKey(),
			'author_id' => $this->integer()->notNull(),
			'articles' => $this->integer()->defaultValue(0),
			'news' => $this->integer()->defaultValue(0),
			'blogs' => $this->integer()->defaultValue(0),
			'projects' => $this->integer()->defaultValue(0),
			'plugins' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_author_stat->author_id', '{{%content_author_stat}}', ['author_id']);
		
		// create pk
		$this->addForeignKey('fk_content_author_stat_author_id', '{{%content_author_stat}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
		
		$users = User::find()
			->joinWith([
				'contentsArticles',
				'contentsNews',
				'contentsBlogs',
				'contentsProjects',
				'contentsPlugins'
			])
			->andWhere('deleted_at IS NULL AND (articles.id IS NOT NULL OR news.id IS NOT NULL AND blogs.id IS NOT NULL AND projects.id IS NOT NULL OR plugins.id IS NOT NULL)')
			->all();
		foreach ($users as $user) {
			ContentAuthorStat::updateLinks($user);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk_content_author_stat_author_id', '{{%content_author_stat}}');
			
		// drop table
		$this->dropTable('{{%content_author_stat}}');
	}
}
