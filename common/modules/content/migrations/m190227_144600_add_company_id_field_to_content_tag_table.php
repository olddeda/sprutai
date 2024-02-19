<?php
namespace common\modules\content\migrations;

use common\modules\content\models\ContentAuthorStat;
use common\modules\user\models\User;
use yii\db\Migration;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;

/**
 * Handles the creation field for table `content_tag`.
 */
class m190227_144600_add_company_id_field_to_content_tag_table extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		$this->addColumn('{{%content_tag}}', 'company_id', $this->integer()->defaultValue(0)->after('author_id'));
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		$this->dropColumn('{{%content_tag}}', 'company_id');
	}
}
