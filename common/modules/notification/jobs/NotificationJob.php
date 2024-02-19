<?php
namespace common\modules\notification\jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;

use common\modules\notification\components\NotificationEvent;

/**
 * Class NotificationJob.
 */
class NotificationJob extends BaseObject implements JobInterface
{
    public $triggerClass;

    public $name;

    public $data;

    /**
     * @inheritdoc
     */
    public function execute($queue) {
		$notification = new NotificationEvent($this->data);
		NotificationEvent::trigger($this->triggerClass, $this->name, $notification);
    }
}
