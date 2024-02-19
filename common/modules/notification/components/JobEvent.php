<?php
namespace common\modules\notification\components;

use yii\base\Event;

/**
 * Class JobEvent
 * @package common\modules\notification\components
 */
class JobEvent extends Event
{
    /**
     * @var string|null unique id of a job
     */
    public $provider;

    /**
     * @var string|null unique id of an event
     */
    public $event;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var array
     */
    public $status = [];

    /**
     * @var boolean whether to continue send messages. Event handlers of
     * [[\common\modules\notification\Module::EVENT_BEFORE_SEND]] may set this property to decide whether
     * to continue running the send message.
     */
    public $isValid = true;
}