<?php
namespace common\modules\base\components\jira;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\BufferStream;

use common\modules\base\components\httpclient\Event;
use common\modules\base\components\Debug;

/**
 * Client component for Jira REST API
 * @package common\modules\base\components\jira
 *
 * Api docs: https://docs.atlassian.com/jira/REST/latest/
 */
class Client extends Component
{
	/**
	 * @var string
	 */
	public $jiraUrl;
	
	/**
	 * @var string
	 */
	public $username;
	
	/**
	 * @var string
	 */
	public $password;
	
	/**
	 * @var string
	 */
	public $httpClientId = 'httpclient';
	
	/**
	 * @var int
	 */
	public $cacheDuration = 60;
	
	/**
	 * @return string
	 */
	public function getApiEndpointUrl() {
		return rtrim($this->jiraUrl, '/').'/rest/api/2/';
	}
	
	/**
	 * @param $path
	 *
	 * @return string
	 */
	public function getUrlOfPath($path) {
		return $this->getApiEndpointUrl().ltrim($path, '/');
	}
	
	/**
	 * @param $path
	 * @param array $params
	 *
	 * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function get($path, $params = []) {
		if (!empty($params)) {
			$params = http_build_query($params);
			$path .= '?'.$params;
		}
		return $this->request('GET', $path);
	}
	
	/**
	 * @param $path
	 * @param array $body
	 *
	 * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function post($path, $body = [], $multipart = null) {
		return $this->request('POST', $path, $body, $multipart);
	}
	
	/**
	 * @param $path
	 * @param array $body
	 *
	 * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function delete($path, $body = []) {
		return $this->request('DELETE', $path, $body);
	}
	
	/**
	 * @param $path
	 * @param array $body
	 *
	 * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function put($path, $body = []) {
		return $this->request('PUT', $path, $body);
	}
	
	/**
	 * @param string $key
	 *
	 * @return Project|null
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getProject($key) {
		$data = $this->get("project/{$key}");
		if (!isset($data['id'])) {
			return null;
		} else {
			return Project::populate($this, $data);
		}
	}
	
	/**
	 * @param string $method
	 * @param string $path
	 * @param array $body
	 * @param $multipart
	 *
	 * @return bool|mixed|\Psr\Http\Message\ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function request($method, $path, $body = [], $multipart = null) {
		$url = $this->getUrlOfPath($path);
		
		if (is_array($body) && !empty($body)) {
			$body = Json::encode($body);
		}
		
		$cacheKeyParams = $method.$url;
		if (!empty($body))
			$cacheKeyParams .= $body;
		$cacheKey = md5($cacheKeyParams);
		$result = Yii::$app->cache->get($cacheKey);
		if ($result !== false) {
			return $result;
		}
		
		try {
			$result = $this->getHttpClient()->request($method, $url, null, [], function (Event $event) use ($body, $multipart) {
				
				$authString = base64_encode($this->username.':'.$this->password);
				
				/** @var \GuzzleHttp\Psr7\Request $request */
				$request = $event->message
					->withAddedHeader('Authorization', 'Basic '.$authString);
				
				if (!empty($body)) {
					$stream = new BufferStream();
					$stream->write($body);
					
					$request = $request->withBody($stream);
				}
				
				if (!empty($multipart)) {
					$request = $request->withBody($multipart)
						//->withAddedHeader('Content-Type', 'multipart/form-data')
						->withAddedHeader('X-Atlassian-Token', 'nocheck');
				}
				else {
					$request = $request
						->withAddedHeader('Accept', 'application/json')
						->withAddedHeader('Content-Type', 'application/json');
				}
				
				return $request;
			});
			
			if (is_string($result)) {
				$result = Json::decode($result);
			}
			
			$message = $url;
			if (!empty($body))
				$message .= PHP_EOL.$body;
			Yii::debug($message, __CLASS__);
			
		} catch (RequestException $e) {
			$result = $e->getResponse()->getBody()->__toString();
			$contentType = $e->getResponse()->getHeader('Content-Type');
			if (!is_array($contentType) && strpos($contentType, 'application/json') !== false) {
				$result = Json::decode($result);
			}
			
			\Yii::error($result, __CLASS__);
		}
		
		Yii::$app->cache->set($cacheKey, $result, $this->cacheDuration);
		
		return $result;
	}
	
	/**
	 * @return \common\modules\base\components\httpclient\Client
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getHttpClient() {
		return Yii::$app->get($this->httpClientId);
	}
	
	/**
	 * @param $value
	 *
	 * @return string
	 */
	public static function escapeValue($value) {
		return strtr($value, [
			'/' => '\u002f',
			'.' => '\u002e',
		]);
	}
	
}
