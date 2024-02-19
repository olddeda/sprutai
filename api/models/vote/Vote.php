<?php
namespace api\models\vote;

use common\modules\vote\models\Vote as BaseVote;
use common\modules\vote\models\VoteAggregate;


/**
 * Class Vote
 * @package api\models\vote
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="module_type", type="integer", description="Тип модуля", enum={40, 81}),
 *     @OA\Property(property="module_id", type="integer", description="ID модуля"),
 *     @OA\Property(property="value", type="integer", description="Значение")
 * )
 */
class Vote extends BaseVote
{
    public function fields() {
        return [
            'likes' => function($data) {
                return $data->aggregate->positive;
            },
            'dislikes' => function($data) {
                return $data->aggregate->negative;
            },
            'has_like' => function($data) {
                return is_null($data->value) ? false : $data->value == 1;
            },
            'has_dislike' => function($data) {
                return is_null($data->value) ? false : $data->value == 0;
            }
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAggregate() {
        return $this->hasOne(VoteAggregate::class, [
            VoteAggregate::tableName().'.entity' => 'entity',
            VoteAggregate::tableName().'.entity_id' => 'entity_id'
        ]);
    }
}