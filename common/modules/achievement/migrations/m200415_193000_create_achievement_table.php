<?php
namespace common\modules\achievement\migrations;

use yii\db\Migration;

use common\modules\achievement\helpers\enum\Type;

/**
 * Handles the creation for table `achievement`.
 */
class m200415_193000_create_achievement_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->createTable('{{%achievement}}', [
            'id' => $this->primaryKey(),
			'type' => $this->integer()->notNull(),
			'title' => $this->string(255)->notNull(),
            'level' => $this->integer()->defaultValue(0),
            'sequence' => $this->integer()->defaultValue(0),
            'status' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
           	'updated_at' => $this->integer(),
        ]);

		$this->createIndex('idx-achievement-type', '{{%achievement}}', ['type']);

		$i = 0;
		$time = time();
        $inserts = [
            [Type::OWNER, 'Владелец 1 устройства', 1, 1, 1, $time, $time],
            [Type::OWNER, 'Владелец 5 устройств', 5, 2, 1, $time, $time],
            [Type::OWNER, 'Владелец 10 устройств', 10, 3, 1, $time, $time],
            [Type::OWNER, 'Владелец 25 устройств', 25, 4, 1, $time, $time],
            [Type::OWNER, 'Владелец 50 устройств', 50, 5, 1, $time, $time],
            [Type::OWNER, 'Владелец 100 устройств', 100, 6, 1, $time, $time],
            [Type::REVIEW, 'Случайный критик', 1, 1, 1, $time, $time],
            [Type::REVIEW, 'Дебютирующий критик', 5, 2, 1, $time, $time],
            [Type::REVIEW, 'Начинающий критик', 10, 3, 1, $time, $time],
            [Type::REVIEW, 'Старательный критик', 25, 4, 1, $time, $time],
            [Type::REVIEW, 'Рьяный критик', 50, 5, 1, $time, $time],
            [Type::REVIEW, 'Опытный критик', 100, 6, 1, $time, $time],
            [Type::ARTICLE, 'Случайный писатель', 1, 1, 1, $time, $time],
            [Type::ARTICLE, 'Дебютирующий писатель', 5, 2, 1, $time, $time],
            [Type::ARTICLE, 'Начинающий писатель', 10, 3, 1, $time, $time],
            [Type::ARTICLE, 'Старательный писатель', 25, 4, 1, $time, $time],
            [Type::ARTICLE, 'Рьяный писатель', 50, 5, 1, $time, $time],
            [Type::ARTICLE, 'Опытный писатель', 100, 6, 1, $time, $time],
            [Type::NEWS, 'Случайный журналист', 1, 1, 1, $time, $time],
            [Type::NEWS, 'Дебютирующий журналист', 5, 2, 1, $time, $time],
            [Type::NEWS, 'Начинающий журналист', 10, 3, 1, $time, $time],
            [Type::NEWS, 'Старательный журналист', 25, 4, 1, $time, $time],
            [Type::NEWS, 'Рьяный журналист', 50, 5, 1, $time, $time],
            [Type::NEWS, 'Опытный журналист', 100, 6, 1, $time, $time],
            [Type::BLOG, 'Случайный блогер', 1, 7, 1, $time, $time],
            [Type::BLOG, 'Дебютирующий блогер', 5, 2, 1, $time, $time],
            [Type::BLOG, 'Начинающий блогер', 10, 3, 1, $time, $time],
            [Type::BLOG, 'Старательный блогер', 25, 4, 1, $time, $time],
            [Type::BLOG, 'Рьяный блогер', 50, 4, 1, $time, $time],
            [Type::BLOG, 'Опытный блогер', 100, 5, 1, $time, $time],
            [Type::COMMENT, 'Случайный комментатор', 1, 1, 1, $time, $time],
            [Type::COMMENT, 'Дебютирующий комментатор', 5, 2, 1, $time, $time],
            [Type::COMMENT, 'Начинающий комментатор', 10, 3, 1, $time, $time],
            [Type::COMMENT, 'Старательный комментатор', 25, 4, 1, $time, $time],
            [Type::COMMENT, 'Рьяный комментатор', 50, 5, 1, $time, $time],
            [Type::COMMENT, 'Опытный комментатор', 100, 6, 1, $time, $time],
            [Type::LIKES, 'Случайный оценщик', 1, 1, 1, $time, $time],
            [Type::LIKES, 'Дебютирующий оценщик', 5, 2, 1, $time, $time],
            [Type::LIKES, 'Начинающий оценщик', 10, 3, 1, $time, $time],
            [Type::LIKES, 'Старательный оценщик', 25, 4, 1, $time, $time],
            [Type::LIKES, 'Рьяный оценщик', 50, 5, 1, $time, $time],
            [Type::LIKES, 'Опытный оценщик', 100, 6, 1, $time, $time],
            [Type::LIKED, 'Случайный любимчик', 1, 1, 1, $time, $time],
            [Type::LIKED, 'Дебютирующий любимчик', 5, 2, 1, $time, $time],
            [Type::LIKED, 'Начинающий любимчик', 10, 3, 1, $time, $time],
            [Type::LIKED, 'Старательный любимчик', 25, 4, 1, $time, $time],
            [Type::LIKED, 'Рьяный любимчик', 50, 5, 1, $time, $time],
            [Type::LIKED, 'Опытный любимчик', 100, 6, 1, $time, $time],
            [Type::SUBSCRIBED, 'Случайная знаменитость', 1, 1, 1, $time, $time],
            [Type::SUBSCRIBED, 'Дебютирующая знаменитость', 5, 2, 1, $time, $time],
            [Type::SUBSCRIBED, 'Начинающая знаменитость', 10, 3, 1, $time, $time],
            [Type::SUBSCRIBED, 'Старательная знаменитость', 25, 4, 1, $time, $time],
            [Type::SUBSCRIBED, 'Рьяная знаменитость', 50, 5, 1, $time, $time],
            [Type::SUBSCRIBED, 'Опытная знаменитость', 100, 6, 1, $time, $time],
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
        $this->dropTable('{{%achievement}}');
    }
}
