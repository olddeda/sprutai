<?php
namespace common\modules\achievement\migrations;

use yii\db\Migration;

use common\modules\achievement\helpers\enum\Type;

/**
 * Handles the creation for table `achievement`.
 */
class m200503_221400_add_achievements extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {

		$i = 0;
		$time = time();
        $inserts = [
            [Type::OWNER, 'Владелец 150 устройств', 150, 7, 1, $time, $time],
            [Type::OWNER, 'Владелец 250 устройств', 250, 8, 1, $time, $time],
            [Type::OWNER, 'Владелец 500 устройств', 500, 9, 1, $time, $time],
        ];
        
        $this->batchInsert('{{%achievement}}', [
            'type',
            'title',
            'level',
            'sequence',
            'status',
            'created_at',
            'updated_at',
        ], $inserts);
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
    }
}
