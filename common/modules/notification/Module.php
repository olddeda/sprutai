<?php
namespace common\modules\notification;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Module as BaseModule;
use yii\base\BootstrapInterface;
use yii\db\Expression;
use yii\helpers\Json;

use common\modules\notification\components\JobEvent;
use common\modules\notification\components\NotificationEvent;
use common\modules\notification\components\Provider;
use common\modules\notification\models\NotificationStatus;

class Module extends BaseModule
{
    const EVENT_BEFORE_SEND = 'beforeSend';
    const EVENT_AFTER_SEND = 'afterSend';
	
	/**
	 * @var string
	 */
    public $controllerNamespace = 'common\modules\notification\controllers';
    
    /** @var array */
    public $channelIds = [-1001082506583];
	
	/**
	 * @var array
	 */
    public $providers = [];
	
	/**
	 * @var array
	 */
    private $_providers = [];
	
	/**
     * @param NotificationEvent $notification
     */
    public function sendEvent(NotificationEvent $notification) {
    	
        /** @var Provider $provider */
        $provider = Yii::createObject($notification->data['provider']);
        
        if (!$provider || !$provider->enabled) {
            return;
        }

        /** @var JobEvent $event */
        $event = new JobEvent([
            'provider' => $notification->data['providerName'],
            'event' => $notification->name,
            'params' => $notification,
        ]);

        $this->trigger(self::EVENT_BEFORE_SEND, $event);

        if (!$event->isValid){
            return;
        }

        try {
            $statusId = $this->setProviderStatus($notification);
            $provider->send($notification);
            $this->setProviderStatus($notification, $statusId, $provider->status);
            $event->status = $provider->status;
            $this->trigger(self::EVENT_AFTER_SEND, $event);
            unset($provider, $event);
        } catch (\Exception $e){
            $this->setProviderStatus($notification, $statusId, $e->getMessage());
            if(YII_DEBUG) {
                \Yii::error($e, __METHOD__);
            }
            throw $e;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function provider($name) {
        if (!isset($this->_providers[$name])) {
            if (isset($this->providers[$name])) {
                $this->_providers[$name] = Yii::createObject($this->providers[$name]);
            }
        }
        return $this->_providers[$name];
    }

    /**
     * @param $provider
     */
    public function attachEvents($providerName, $provider) {
        foreach ($provider['events'] as $className => $events) {
            foreach ($events as $eventName) {
                NotificationEvent::on($className, $eventName, [$this, 'sendEvent'], ['providerName' => $providerName, 'provider' => $provider]);
            }
        }
    }

    /**
     * @param $class
     * @return string
     */
    function class_basename($class) {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }

    private function setProviderStatus(NotificationEvent &$notification, $statusId = null, $ret = null) {
		$providerName = $notification->data['providerName'];
		$event = $notification->name;
	
		if (!$statusId) {
			/** @var NotificationStatus $status */
			$status = new NotificationStatus;
			$status->provider = $providerName;
			$status->event = $event;
			$status->params = Json::encode($notification->getAttributes());
		} else {
			/** @var NotificationStatus $status */
			$status = NotificationStatus::findOne($statusId);
			if ($status) {
				$status->updated_at = time();
				$status->status = Json::encode($ret);
			}
		}
	
		if ($status) {
			$status->save();
			return $status->id;
		}

        return 0;
    }

}
