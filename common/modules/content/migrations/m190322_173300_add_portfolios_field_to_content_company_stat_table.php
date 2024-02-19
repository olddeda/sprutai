<?php
namespace common\modules\content\migrations;

use common\modules\content\models\ContentAuthorStat;
use common\modules\user\models\User;
use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation field for table `content_company_stat`.
 */
class m190322_173300_add_portfolios_field_to_content_company_stat_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->addColumn('{{%content_company_stat}}', 'portfolios', $this->integer()->defaultValue(0)->after('plugins'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropColumn('{{%content_company_stat}}', 'portfolios');
	}
}
