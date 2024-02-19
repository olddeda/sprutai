<?php
namespace common\modules\content\migrations;

use common\modules\content\models\ContentAuthorStat;
use common\modules\user\models\User;
use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation field for table `content`.
 */
class m190226_181900_add_company_id_field_to_content_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		$this->addColumn('{{%content}}', 'company_id', $this->integer()->defaultValue(0)->after('type'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		$this->dropColumn('{{%content}}', 'company_id');
	}
}
