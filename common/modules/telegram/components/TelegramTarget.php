<?php
namespace common\modules\telegram\components;

use Yii;
use yii\log\Target;
use yii\base\InvalidConfigException;

/**
 * Class TelegramTarget
 * @package common\modules\telegram\components
 */
class TelegramTarget extends Target
{
	/**
	 * Destination chat id or channel username
	 * @var int|string
	 */
	public $chatId;
	
	/**
	 * Check required properties
	 */
	public function init() {
		parent::init();
	}
	
	/**
	 * Exports log [[messages]] to a specific destination.
	 * Child classes must implement this method.
	 */
	public function export() {
		$messages = array_map([$this, 'formatMessage'], $this->messages);
		
		/** @var \common\modules\base\extensions\telegram\Telegram $telegram */
		$telegram = Yii::$app->telegram;
		
		foreach ($messages as $message) {
			$telegram->sendMessage([
				'chat_id' => $this->chatId,
				'text' => $message,
			]);
		}
	}
}