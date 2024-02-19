<?php
namespace common\modules\notification\providers;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\notification\components\NotificationEvent as NotificationComponent;
use common\modules\notification\components\Provider;
use common\modules\notification\models\Notification;

/**
 * Class WebProvider
 * @package common\modules\notification\providers
 */
class WebProvider extends Provider
{
	/**
	 * @param NotificationComponent $notification
	 */
    public function send(NotificationComponent $notification) {
        if(empty($notification->toId))
        	return;
        
        $toIds = (is_array($notification->toId)) ? $notification->toId : [$notification->toId];
        
        foreach ($toIds as $toId) {
            $message = new Notification();
            $message->from_id = $notification->fromId;
            $message->to_id = $toId;
            $message->event = $notification->name;
            $message->title = $notification->subject;
            $message->message = $notification->message;
            $message->setParams(ArrayHelper::merge(['event' => $notification->name], $notification->params));
            $status = $message->save();
            unset($message);
            $this->status[$toId] = $status;
        }
    }
}