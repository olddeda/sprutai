<?php
namespace common\modules\achievement\migrations;

use yii\db\Migration;

use common\modules\achievement\helpers\enum\Type;

/**
 * Handles the creation for table `achievement`.
 */
class m200429_174500_add_achievements extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {

		$i = 0;
		$time = time();
        $inserts = [
            [Type::REVIEW, 'Бывалый критик', 250, 7, 1, $time, $time],
            [Type::REVIEW, 'Матерый критик', 500, 8, 1, $time, $time],
            [Type::ARTICLE, 'Бывалый писатель', 250, 7, 1, $time, $time],
            [Type::ARTICLE, 'Матерый писатель', 500, 8, 1, $time, $time],
            [Type::NEWS, 'Бывалый журналист', 250, 7, 1, $time, $time],
            [Type::NEWS, 'Матерый журналист', 500, 8, 1, $time, $time],
            [Type::BLOG, 'Бывалый блогер', 50, 4, 1, $time, $time],
            [Type::BLOG, 'Матерый блогер', 100, 5, 1, $time, $time],
            [Type::COMMENT, 'Бывалый комментатор', 250, 7, 1, $time, $time],
            [Type::COMMENT, 'Матерый комментатор', 500, 8, 1, $time, $time],
            [Type::LIKES, 'Бывалый оценщик', 250, 7, 1, $time, $time],
            [Type::LIKES, 'Матерый оценщик', 500, 8, 1, $time, $time],
            [Type::LIKED, 'Бывалый любимчик', 250, 7, 1, $time, $time],
            [Type::LIKED, 'Матерый любимчик', 500, 8, 1, $time, $time],
            [Type::SUBSCRIBED, 'Бывалая знаменитость', 250, 7, 1, $time, $time],
            [Type::SUBSCRIBED, 'Матерая знаменитость', 500, 8, 1, $time, $time],
            [Type::PLUGIN, 'Бывалый плагинодел', 250, 7, 1, $time, $time],
            [Type::PLUGIN, 'Матерый плагинодел', 500, 8, 1, $time, $time],
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
