<?php
namespace common\modules\vote\events;

use yii\base\Event;

use common\modules\vote\models\VoteForm;

/**
 * Class VoteActionEvent
 * @package common\modules\vote\events
 */
class VoteActionEvent extends Event
{
    /**
     * @var VoteForm
     */
    public $voteForm;

    /**
     * @var array
     */
    public $responseData;
}
