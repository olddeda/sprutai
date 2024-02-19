<?php
namespace common\modules\achievement\migrations;

use yii\db\Migration;

use common\modules\achievement\helpers\enum\Type;

/**
 * Handles the creation for table `achievement`.
 */
class m200428_214400_add_achievements extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {

		$i = 0;
		$time = time();
        $inserts = [
            [Type::PLUGIN, 'Случайный плагинодел', 1, 1, 1, $time, $time],
            [Type::PLUGIN, 'Дебютирующий плагинодел', 5, 2, 1, $time, $time],
            [Type::PLUGIN, 'Начинающий плагинодел', 10, 3, 1, $time, $time],
            [Type::PLUGIN, 'Старательный плагинодел', 25, 4, 1, $time, $time],
            [Type::PLUGIN, 'Рьяный плагинодел', 50, 5, 1, $time, $time],
            [Type::PLUGIN, 'Опытный плагинодел', 100, 6, 1, $time, $time],
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
