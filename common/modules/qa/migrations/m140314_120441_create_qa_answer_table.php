<?php

use common\modules\qa\models\Answer;
use common\modules\qa\models\AnswerInterface;
use yii\db\Schema;

class m140314_120441_create_qa_answer_table extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable(Answer::tableName(), [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'question_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'content' => Schema::TYPE_TEXT . ' NOT NULL',
            'votes' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT ' . AnswerInterface::STATUS_PUBLISHED,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable(Answer::tableName());
    }
}
