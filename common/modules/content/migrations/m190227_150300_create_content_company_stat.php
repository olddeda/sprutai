<?php
namespace common\modules\content\migrations;

use yii\db\Command;
use yii\db\Migration;

use common\modules\vote\models\Vote;

use common\modules\content\models\ContentCompanyStat;
use common\modules\company\models\Company;

/**
 * Handles the creation for table `content_company_stat`.
 */
class m190227_150300_create_content_company_stat extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		// create table
		$this->createTable('{{%content_company_stat}}', [
			'id' => $this->primaryKey(),
			'company_id' => $this->integer()->notNull(),
			'articles' => $this->integer()->defaultValue(0),
			'news' => $this->integer()->defaultValue(0),
			'blogs' => $this->integer()->defaultValue(0),
			'projects' => $this->integer()->defaultValue(0),
			'plugins' => $this->integer()->defaultValue(0),
			'subscribers' => $this->integer()->defaultValue(0),
			'created_at' => $this->integer(11),
			'updated_at' => $this->integer(11),
		]);

		// creates indexes
		$this->createIndex('idx-content_company_stat->company_id', '{{%content_company_stat}}', ['company_id']);
		
		// create pk
		$this->addForeignKey('fk_content_company_stat_company_id', '{{%content_company_stat}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'RESTRICT');
		
		$companies = Company::find()
			->joinWith([
				'contentsArticles',
				'contentsNews',
				'contentsBlogs',
				'contentsProjects',
				'contentsPlugins'
			])
			->andWhere(Company::tableName().'.status = 1 AND (articles.id IS NOT NULL OR news.id IS NOT NULL AND blogs.id IS NOT NULL AND projects.id IS NOT NULL OR plugins.id IS NOT NULL)')
			->votes()
			->all();
		foreach ($companies as $company) {
			ContentCompanyStat::updateLinks($company);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		// Drop foreign keys
		$this->dropForeignKey('fk_content_company_stat_company_id', '{{%content_company_stat}}');
			
		// drop table
		$this->dropTable('{{%content_company_stat}}');
	}
}
