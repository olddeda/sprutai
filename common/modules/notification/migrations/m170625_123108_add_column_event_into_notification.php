<?php
namespace common\modules\notification\migrations;

use yii\db\Migration;

/**
 * Class m170625_123108_add_column_event_into_notification
 * @package common\modules\notification\migrations
 */
class m170625_123108_add_column_event_into_notification extends Migration
{
	/**
	 * @inheritdoc
	 */
    public function up() {
        $this->addColumn('{{%notification}}', 'event', $this->string(100)->after('to_id'));
    }
	
	/**
	 * @inheritdoc
	 */
    public function down() {
        $this->dropColumn('{{%notification}}', 'event');
    }

}
