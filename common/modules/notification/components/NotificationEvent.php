<?php

namespace common\modules\notification\components;

use Yii;
use yii\base\Event;
use yii\base\Exception;

use common\modules\notification\jobs\NotificationJob;

/**
 * Class NotificationEvent
 * @package common\modules\notification\components
 */
class NotificationEvent extends Event
{
	/** @var int */
	public $fromId = null;
	
	/** @var array */
	public $toId = [];
	
	/** @var array */
	public $from = [];
	
	/** @var array */
	public $to = [];
	
	/** @var array */
	public $phone = [];
	
	/** @var array */
	public $token = [];
	
	/** @var string */
	public $subject = '';
	
	/** @var string */
	public $message = '';
	
	/** @var string */
	public $path;
	
	/** @var array */
	public $layouts;
	
	/** @var array */
	public $view;
	
	/** @var array */
	public $params = [];
	
	/** @var array */
	public $push = [
		'aps' => [
			'alert' => 'Hi',
			'badge' => 1,
			'sound' => 'default',
			"link_url" => "https://google.com"
		],
	];
	
	/**
	 * @throws Exception
	 */
	public function init() {
		if (empty($this->from)) {
			if (!empty(Yii::$app->params['email.noreply'])) {
				$this->from = [Yii::$app->params['email.noreply'] => Yii::$app->name];
				$this->fromId = 0;
			} else {
				throw new Exception("Sender email not found");
			}
		}
		if (!isset($this->fromId)) {
			throw new Exception("Sender ID not found");
		}
	}
	
	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getAttributes() {
		$properties = [];
		
		$reflect = new \ReflectionClass($this);
		$props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
		
		foreach ($props as $prop) {
			$properties[$prop->name] = $this->{$prop->name};
		}
		
		return $properties;
	}
}
