<?php
namespace common\modules\comments\migrations;

use common\modules\content\models\ContentAuthorStat;
use common\modules\user\models\User;
use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation field for table `comment`.
 */
class m190312_152600_add_company_id_field_to_comment_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		$this->addColumn('{{%comment}}', 'company_id', $this->integer()->defaultValue(0)->after('parent_id'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		$this->dropColumn('{{%comment}}', 'parent_id');
	}
}
