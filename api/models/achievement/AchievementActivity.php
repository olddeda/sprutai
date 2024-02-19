<?php
namespace api\models\achievement;

use api\models\achievement\Achievement as BaseModel;

/**
 * Class AchievementActivity
 * @package api\models\achievement
 */
class AchievementActivity extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'type' => function ($data) {
                return $data->getTypeName();
            },
            'title',
            'level',
        ];
    }
}