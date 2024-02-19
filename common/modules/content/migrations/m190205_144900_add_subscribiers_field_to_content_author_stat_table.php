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
class m190205_144900_add_subscribiers_field_to_content_author_stat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		$this->addColumn('{{%content_author_stat}}', 'subscribers', $this->integer()->defaultValue(0)->after('plugins'));
		
		$users = User::find()
			->joinWith([
				'contentsArticles',
				'contentsNews',
				'contentsBlogs',
				'contentsProjects',
				'contentsPlugins'
			])
			->andWhere('deleted_at IS NULL')
			->all();
		foreach ($users as $user) {
			ContentAuthorStat::updateLinks($user);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		$this->dropColumn('{{%content_author_stat}}', 'subscribers');
	}
}
