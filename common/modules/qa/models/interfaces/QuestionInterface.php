<?php
namespace common\modules\qa\models\interfaces;

/**
 * Interface QuestionInterface
 * @package common\modules\qa\models
 */
interface QuestionInterface
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @param $id
     * @return mixed
     */
    public static function incrementAnswers($id);

    /**
     * @param $id
     * @return mixed
     */
    public static function decrementAnswers($id);

    /**
     * @param $attribute
     * @param $params
     * @return mixed
     */
    public function normalizeTags($attribute, $params);

    /**
     * @return mixed
     */
    public function isAuthor();

    /**
     * @param false $user
     * @return mixed
     */
    public function isFavorite($user = false);

    /**
     * @param $data
     * @return mixed
     */
    public function haveDraft($data);

    /**
     * @return mixed
     */
    public function isDraft();

    /**
     * @return mixed
     */
    public function isUserUnique();

    /**
     * @return mixed
     */
    public function toggleFavorite();

    /**
     * @return mixed
     */
    public function getTagsList();

    /**
     * @return mixed
     */
    public function getUserName();

    /**
     * @return mixed
     */
    public function getAnswers();

    /**
     * @return mixed
     */
    public function getUser();

    /**
     * @return mixed
     */
    public function getFavorite();

    /**
     * @return mixed
     */
    public function getFavorites();

    /**
     * @return mixed
     */
    public function getFavoriteCount();
}
