<?php
namespace common\modules\notification\providers;

use Yii;

use common\modules\notification\components\NotificationEvent;
use common\modules\notification\components\Provider;

/**
 * Class PushProvider
 * @package common\modules\notification\providers
 */
class PushProvider extends Provider
{
    /**
     * @param NotificationEvent $notification
     */
    public function send(NotificationEvent $notification) {
        if(empty($notification->token)) return;

        /** @var \common\modules\notification\components\Push $push */
        $push = Yii::createObject(array_merge(['class' => 'common\modules\notification\components\Push'], $this->config));
        
        $tokens = (is_array($notification->token)) ? $notification->token : [$notification->token];
        foreach ($tokens as $token){
            $status = $push->ios()->send($token, $notification->push);
            $this->status[$token] = $status;
        }
        unset($push);
    }
}