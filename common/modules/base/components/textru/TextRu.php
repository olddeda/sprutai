<?php
namespace common\modules\base\components\textru;

use yii\base\Component;
use yii\base\InvalidConfigException;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;

use common\modules\base\components\Debug;

/**
 * Class TextRu
 * @package common\modules\base\extensions\textru
 */
class TextRu extends Component {
	
	/**
	 * @var string
	 */
	public $apiUrl = 'http://api.text.ru/';
	
	/**
	 * @var string
	 */
	public $userKey;
	
	/**
	 * @var yii\httpclient\Client
	 */
	private $_client;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		if ($this->apiUrl === null)
			throw new InvalidConfigException('The "apiUrl" property must be set.');
		
		if ($this->userKey === null)
			throw new InvalidConfigException('The "userKey" property must be set.');
		
		$this->_client = new Client([
			'baseUrl' => $this->apiUrl,
			'responseConfig' => [
				'format' => Client::FORMAT_JSON
			],
		]);
		
		parent::init();
	}
	
	/**
	 * @param string $text
	 *
	 * @return object
	 */
	public function queue(string $text, array $exceptUrls) : object {
		return $this->_send([
			'text' => $text,
			'excepturl' => implode(',', $exceptUrls),
			'visible' => 'vis_on',
		]);
	}
	
	/**
	 * @param string $uid
	 *
	 * @return object
	 */
	public function result(string $uid) : object {
		return $this->_send([
			'uid' => $uid,
			'jsonvisible' => 'detail',
		]);
	}
	
	/**
	 * @param array $data
	 *
	 * @return object
	 */
	private function _send($data) : object {
		$data = ArrayHelper::merge($data, [
			'userkey' => $this->userKey,
		]);
		
		$response = $this->_client->createRequest()
			->setMethod('POST')
			->setUrl('post')
			->setData($data)
			->send();
		
		$result = Json::decode($response->content);
		
		$error = [
			'code' => 0,
		];
		
		if (isset($result['error_code'])) {
			$error['code'] = $result['error_code'];
			unset($result['error_code']);
		}
		
		if (isset($result['error_desc'])) {
			$error['text'] = $result['error_desc'];
			unset($result['error_desc']);
		}
		$result['error'] = $error;
		
		$data = null;
		if (isset($result['result_json'])) {
			$data = Json::decode($result['result_json']);
			unset($result['result_json']);
		}
		$result['data'] = $data;
		
		$seo = null;
		if (isset($result['seo_check'])) {
			$seo = Json::decode($result['seo_check']);
			unset($result['seo_check']);
		}
		$result['seo'] = $seo;
		
		$spellcheck = null;
		if (isset($result['spell_check'])) {
			$spellcheck = Json::decode($result['spell_check']);
			unset($result['spell_check']);
		}
		$result['spellcheck'] = $spellcheck;
		
		return Json::decode(Json::encode($result), false);
	}
}
