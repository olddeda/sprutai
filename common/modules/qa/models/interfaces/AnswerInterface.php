<?php
namespace common\modules\qa\models\interfaces;

use yii\db\ActiveQuery;

/**
 * Interface AnswerInterface
 * @package common\modules\qa\models\interfaces
 */
interface AnswerInterface
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @param $questionID
     * @return mixed
     */
    public static function removeRelation($questionID);

    /**
     * @param ActiveQuery $query
     * @param $order
     * @return mixed
     */
    public static function applyOrder(ActiveQuery $query, $order);

    /**
     * @return mixed
     */
    public function isAuthor();

    /**
     * @return mixed
     */
    public function isCorrect();

    /**
     * @return mixed
     */
    public function toggleCorrect();

    /**
     * @return mixed
     */
    public function getUser();

    /**
     * @return mixed
     */
    public function getUserName();

    /**
     * @return mixed
     */
    public function getQuestion();
}
