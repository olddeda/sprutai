<?php
namespace common\modules\notification\components;

use yii\base\Component;
use yii\base\Exception;

abstract class Provider extends Component
{
    /** @var array  */
    public $config = [];

    /** @var array */
    public $events = [];

    /** @var string|array */
    public $status = [];

    /** @var bool */
    public $enabled = true;

    public function send(NotificationEvent $notification){
        throw new Exception('Not found notification handler');
    }
}