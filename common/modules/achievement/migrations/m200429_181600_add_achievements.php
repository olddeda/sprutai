<?php
namespace common\modules\achievement\migrations;

use yii\db\Migration;

use common\modules\achievement\helpers\enum\Type;

/**
 * Handles the creation for table `achievement`.
 */
class m200429_181600_add_achievements extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {

		$i = 0;
		$time = time();
        $inserts = [
            [Type::LIKES, 'Всеобщий оценщик', 1000, 9, 1, $time, $time],
            [Type::LIKES, 'Галактический оценщик', 5000, 10, 1, $time, $time],
            [Type::LIKED, 'Всеобщий любимчик', 1000, 9, 1, $time, $time],
            [Type::LIKED, 'Галактический любимчик', 5000, 10, 1, $time, $time],
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
