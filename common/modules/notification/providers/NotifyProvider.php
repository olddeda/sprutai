<?php
namespace common\modules\notification\providers;

use Yii;

use common\modules\notification\components\Provider;
use common\modules\notification\components\NotificationEvent;

/**
 * Class NotifyProvider
 * @package common\modules\notification\providers
 */
class NotifyProvider extends Provider
{
    /**
     * @param NotificationEvent $notification
     */
    public function send(NotificationEvent $notification) {
        if (empty($notification->notify))
        	return;
        Yii::$app->session->addFlash($notification->notify[0], $notification->notify[1]);
    }
}