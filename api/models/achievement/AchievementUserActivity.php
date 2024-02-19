<?php
namespace api\models\achievement;

use api\models\achievement\AchievementUser as BaseModel;

/**
 * Class AchievementUserActivity
 * @package api\models\achievement
 */
class AchievementUserActivity extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'user_id',
        ];
    }
}