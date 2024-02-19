<?php
namespace api\models\comment;

use common\modules\base\components\Debug;
use common\modules\vote\models\Vote;
use Yii;

/**
 * Class CommentActivity
 * @package api\models\comment
 */
class CommentActivity extends Comment
{
    /**
     * @inheritdoc
     *
     * The default implementation returns the names of the columns whose values have been populated into this record.
     */
    public function fields() {
        return [
            'id',
            'level',
            'rating' => function ($data) {
                $ratingAggregate = $data->getVoteAggregate(Vote::COMMENT_RATING);

                $rating = 0;
                if ($data->rating) {
                    $rating = $data->rating;
                }
                else if ($ratingAggregate && $ratingAggregate->rating) {
                    $rating = $ratingAggregate->rating;
                }

                return $rating;
            },
            'content' => function($data) {
                return $data->getContent_parsed();
            },
            'created_at',
        ];
    }
}
