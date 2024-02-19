<?php
namespace common\modules\base\components\social\slack;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\log\Logger;

/**
 * Slack client allows to send messages directly to Slack chats
 */
class Client extends Component {
	/**
	 * @var string URL of Slack incoming webhook integration
	 */
	public $url;
	
	/**
	 * @var string sender username
	 */
	public $username;
	
	/**
	 * @var string emoji code to use as avatar of sender
	 */
	public $emoji;
	
	/**
	 * @var string Default message content. Useful when sending only attachments
	 */
	public $defaultText = "Message from Yii application";
	
	/**
	 * @var string Default channel to send messages to
	 */
	public $defaultChannel;
	
	/** @var string|\yii\httpclient\Client */
	public $httpclient = 'httpclient';
	
	/**
	 * @inheritdoc
	 * @throws InvalidConfigException
	 */
	public function init() {
		if (is_string($this->httpclient)) {
			$this->httpclient = Yii::$app->get($this->httpclient);
		}
		elseif (is_array($this->httpclient)) {
			if ( ! isset($this->httpclient['class'])) {
				$this->httpclient['class'] = 'yii\httpclient\Client';
			}
			$this->httpclient = Yii::createObject($this->httpclient);
		}
		if ( ! $this->httpclient instanceof \yii\httpclient\Client) {
			throw new InvalidConfigException("Client::httpclient must be either a Http client instance or the application component ID of a Http client.");
		}
	}
	
	/**
	 * Send a message to slack
	 *
	 * @param string $text message text
	 * @param string $emoji emoji icon
	 * @param array $attachments attachments (@see https://api.slack.com/incoming-webhooks)
	 * @param string $channel channel to send to
	 */
	public function send($text = null, $emoji = null, $attachments = [], $channel = null) {
		$this->httpclient->post($this->url, [
			'payload' => Json::encode($this->getPayload($text, $emoji, $attachments, $channel)),
		])->send();
	}
	
	/**
	 * Send a message info to slack
	 * @param $title
	 * @param $text
	 */
	public function info($title, $text) {
		$this->send($title, null, [
			[
				'text' => $text,
				'color' => 'good',
			]
		]);
	}
	
	/**
	 * @param null $text
	 * @param null $emoji
	 * @param array $attachments
	 * @param null $channel
	 *
	 * @return array
	 */
	protected function getPayload($text = null, $emoji = null, $attachments = [], $channel = null) {
		if ($text === null) {
			$text = $this->defaultText;
		}
		if ($channel === null) {
			$channel = $this->defaultChannel;
		}
		
		$payload = [
			'text' => $text,
			'username' => $this->username,
			'attachments' => $attachments,
		];
		if ($channel !== null) {
			$payload['channel'] = $channel;
		}
		if ($emoji !== null) {
			$payload['icon_emoji'] = $emoji;
		}
		
		return $payload;
	}
	
}
