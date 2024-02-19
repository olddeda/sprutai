<?php
namespace common\modules\base\components\httpclient;

use Psr\Http\Message\MessageInterface;

/**
 * Class Event
 * @package common\modules\base\components\httpclient
 */
class Event extends \yii\base\Event
{

    /**
     * @var MessageInterface
     */
    public $message;
	
	/**
	 * @var bool
	 */
    public $isValid = true;

}
