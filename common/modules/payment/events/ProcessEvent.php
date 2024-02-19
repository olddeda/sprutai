<?php
namespace common\modules\payment\events;

use yii\base\Event;

use common\modules\payment\components\Process;
use common\modules\payment\components\Request;

/**
 * Class ProcessEvent
 * @package common\modules\payment\events
 */
class ProcessEvent extends Event
{

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Process
     */
    public $process;
}
