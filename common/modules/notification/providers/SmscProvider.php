<?php
namespace common\modules\notification\providers;

use Yii;
use yii\base\Component;

use common\modules\notification\components\NotificationEvent;
use common\modules\notification\components\Provider;

/**
 * Class SmscProvider
 * @package common\modules\notification\providers
 */
class SmscProvider extends Provider
{
    public $config = [
        'login' => '',
        'password' => '',
        'post' => true,
        'https' => true,
        'charset' => 'utf-8',
        'debug' => false,
    ];

    /**
     * @param NotificationEvent $notification
     */
    public function send(NotificationEvent $notification) {
        if(empty($notification->phone)) return;

        /** @var \ladamalina\smsc\Smsc $sms */
        $sms = Yii::createObject(array_merge(['class' => 'ladamalina\smsc\Smsc'], $this->config));

        $phones = (is_array($notification->phone)) ? $notification->phone : [$notification->phone];
        foreach ($phones as $phone){
            $result = $sms->send_sms($phone, $notification->subject);
            $status = $sms->isSuccess($result);
            $this->status[$phone] = $status;
        }

        unset($sms);
    }

}