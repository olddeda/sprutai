<?php
namespace common\modules\base\components\httpclient;

use common\modules\base\components\Debug;
use InvalidArgumentException;

use Yii;
use yii\base\Arrayable;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Client class is an interface designed for performing flexible HTTP requests
 *
 * Following shortcuts available via magic __call method:
 *
 * @method get($url, $options = [], $detectMimeType = null) makes GET request to given url. see [[request()]] for arguments explanation
 * @method getAsync($url, $options = []) makes asynchronous GET request to given url. see [[requestAsync()]] for arguments explanation
 * @method post($url, $body = null, $options = [], $detectMimeType = null) makes POST request to given url. see [[request()]] for arguments explanation
 * @method postAsync($url, $body = null, $options = []) makes asynchronous POST request to given url. see [[requestAsync()]] for arguments explanation
 * @method put($url, $body = null, $options = [], $detectMimeType = null) makes PUT request to given url. see [[request()]] for arguments explanation
 * @method putAsync($url, $body = null, $options = []) makes asynchronous PUT request to given url. see [[requestAsync()]] for arguments explanation
 * @method delete($url, $body = null, $options = [], $detectMimeType = null) makes DELETE request to given url. see [[request()]] for arguments explanation
 * @method deleteAsync($url, $body = null, $options = []) makes asynchronous DELETE request to given url. see [[requestAsync()]] for arguments explanation
 * @method options($url, $body = null, $options = [], $detectMimeType = null) makes OPTIONS request to given url. see [[request()]] for arguments explanation
 * @method optionsAsync($url, $body = null, $options = []) makes asynchronous OPTIONS request to given url. see [[requestAsync()]] for arguments explanation
 * @method head($url, $body = null, $options = [], $detectMimeType = null) makes HEAD request to given url. see [[request()]] for arguments explanation
 * @method headAsync($url, $body = null, $options = []) makes asynchronous HEAD request to given url. see [[requestAsync()]] for arguments explanation
 *
 * You can make any other HTTP request in same manner
 */
class Client extends Component
{
	const EVENT_BEFORE_REQUEST = 'beforeRequest';
	const EVENT_AFTER_REQUEST = 'afterRequest';
	
	/**
	 * @var array|ClientInterface GuzzleHttp config and instance
	 */
	public $client = [];
	
	/**
	 * @var array
	 */
	public $requestOptions = [];
	
	/**
	 * @var string
	 */
	public $baseUrl;
	
	/**
	 * @var array
	 */
	public $requestHeaders = [];
	
	/**
	 * @var bool
	 */
	public $detectMimeType = true;
	
	/**
	 * @var string
	 */
	public $httpVersion = '1.1';
	
	public function getClient() {
		if (is_array($this->client)) {
			if (!isset($this->client['class'])) {
				$this->client['class'] = "\\GuzzleHttp\\Client";
			}
			$this->client = Yii::createObject($this->client);
		}
		return $this->client;
	}
	
	/**
	 * @param string $method
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (!isset($args[0])) {
			throw new InvalidArgumentException("Url is not specified");
		}
		$methodName = $method;
		$request = 'request';
		if (substr($methodName, -5) === 'Async') {
			$methodName = substr($methodName, 0, -5);
			$request .= 'Async';
		}
		$methodName = strtoupper(implode('-', preg_split('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', $methodName)));
		$url = $args[0];
		if ($methodName === 'GET') {
			$body = null;
			$options = isset($args[1]) ? $args[1] : [];
			$detectMimeType = isset($args[2]) ? $args[2] : true;
		} else {
			$body = isset($args[1]) ? $args[1] : null;
			$options = isset($args[2]) ? $args[2] : [];
			$detectMimeType = isset($args[3]) ? $args[3] : true;
		}
		return $this->$request($methodName, $url, $body, $options, $detectMimeType);
	}
	
	/**
	 * @param $body
	 * @param $options
	 *
	 * @return string
	 */
	public static function serialize($body, &$options) {
		$options['headers']['content-type'] = 'application/json';
		if ($body instanceof Arrayable) {
			return Json::encode($body->toArray());
		} else {
			return Json::encode($body);
		}
	}
	
	/**
	 * @param $options
	 */
	protected function prepareOptions(&$options) {
		$options = ArrayHelper::merge($this->requestOptions, $options);
		if (isset($options['headers'])) {
			$options['headers'] = ArrayHelper::merge($options['headers'], $this->requestHeaders);
		} else {
			$options['headers'] = $this->requestHeaders;
		}
	}
	
	/**
	 * @param $body
	 * @param $options
	 *
	 * @return string|null
	 */
	protected function prepareBody($body, &$options) {
		if (is_scalar($body)) {
			return $body;
		}
		if (is_array($body)) {
			$options['form_params'] = $body;
			return null;
		}
		if (is_object($body)) {
			
			$options['headers']['content-type'] = 'application/json';
			if ($body instanceof Arrayable) {
				return Json::encode($body->toArray());
			} else {
				return Json::encode($body);
			}
		}
		return $body;
	}
	
	/**
	 * @param ResponseInterface $response
	 *
	 * @return \SimpleXMLElement|string
	 */
	public function formatResponse(ResponseInterface $response) {
		$contentType = $response->getHeader('Content-Type');
		if (sizeof($contentType)) {
			$contentType = array_shift($contentType);
			if (preg_match('/^([a-z-]+\/[a-z-]+)/', $contentType, $matches)) {
				$mimeType = $matches[1];
			} else {
				$mimeType = null;
			}
		} else {
			$mimeType = null;
		}
		switch ($mimeType) {
			case 'application/json':
				try {
					return Json::decode((string)$response->getBody());
				} catch (InvalidArgumentException $e) {
					return false;
				}
			case 'application/xml':
			case 'application/atom+xml':
			case 'application/soap+xml':
			case 'application/xhtml+xml':
			case 'application/xml-dtd':
			case 'application/xop+xml':
			case 'text/xml':
				return simplexml_load_string((string)$response->getBody());
		}
		return (string)$response->getBody();
	}
	
	/**
	 * @param $method
	 * @param $url
	 * @param null $body
	 * @param array $headers
	 *
	 * @return Request
	 */
	public function createRequest($method, $url, $body = null, $headers = []) {
		return new Request($method, $url, ArrayHelper::merge($this->requestHeaders, $headers), $body, $this->httpVersion);
	}
	
	/**
	 * @param $method
	 * @param $url
	 * @param null $body
	 * @param array $options
	 * @param null $detectMimeType
	 *
	 * @return bool|ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function request($method, $url, $body = null, $options = [], $beforeRequest = null, $detectMimeType = null) {
		$body = $this->prepareBody($body, $options);
		
		$request = $this->createRequest($method, $url, $body);
		
		if (!is_null($beforeRequest)) {
			$event = new Event([
				'message' => $request,
			]);
			$request = call_user_func($beforeRequest, $event);
		}
		
		return $this->send($request, $options, $detectMimeType);
	}
	
	/**
	 * @param string $url
	 * @param string $method
	 * @param callable|null $beforeRequest
	 * @param array $options
	 * @return mixed
	 */
	public function request_old($method = 'GET', $url, $beforeRequest = null, $options = [])
	{
		$format = !isset($options['format']) || $options['format'];
		unset($options['format']);
		try {
			$request = $this->getClient()->createRequest($method, $url, $options);
			$event = new Event([
				'message' => $request,
			]);
			if ($beforeRequest !== null) {
				if (call_user_func($beforeRequest, $event) === false) {
					return false;
				}
			}
			$this->trigger(static::EVENT_BEFORE_REQUEST, $event);
			$response = $this->getClient()->send($request);
		} catch (RequestException $e) {
			throw $e;
		}
		$this->trigger(static::EVENT_AFTER_REQUEST, new Event([
			'message' => $response
		]));
		if ($format) {
			return $this->formatResponse($response);
		} else {
			return $response;
		}
	}
	
	/**
	 * @param $method
	 * @param $url
	 * @param null $body
	 * @param array $options
	 *
	 * @return bool|\GuzzleHttp\Promise\PromiseInterface
	 */
	public function requestAsync($method, $url, $body = null, $options = []) {
		$body = $this->prepareBody($body, $options);
		$request = $this->createRequest($method, $url, $body);
		return $this->sendAsync($request, $options);
	}
	
	/**
	 * @param RequestInterface $request
	 *
	 * @return bool
	 */
	public function beforeRequest(RequestInterface &$request) {
		$event = new Event([
			'message' => &$request,
		]);
		$this->trigger(static::EVENT_BEFORE_REQUEST, $event);
		return $event->isValid;
	}
	
	/**
	 * @param ResponseInterface $response
	 */
	public function afterRequest(ResponseInterface $response) {
		$this->trigger(static::EVENT_AFTER_REQUEST, new Event([
			'message' => $response
		]));
	}
	
	/**
	 * @param RequestInterface $request
	 * @param array $options
	 * @param null $detectMimeType
	 *
	 * @return bool|ResponseInterface|\SimpleXMLElement|string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function send(RequestInterface $request, $options = [], $detectMimeType = null) {
		if (!$this->beforeRequest($request)) {
			return false;
		}
		$this->prepareOptions($options);
		$response = $this->getClient()->send($request, $options);
		$this->afterRequest($response);
		if ($detectMimeType === null) {
			$detectMimeType = $this->detectMimeType;
		}
		if ($detectMimeType) {
			return $this->formatResponse($response);
		} else {
			return $response;
		}
	}
	
	/**
	 * @param RequestInterface $request
	 * @param array $options
	 *
	 * @return bool|\GuzzleHttp\Promise\PromiseInterface
	 */
	public function sendAsync(RequestInterface $request, $options = []) {
		if (!$this->beforeRequest($request)) {
			return false;
		}
		$this->prepareOptions($options);
		$promise = $this->getClient()->sendAsync($request, $options);
		$promise->then([$this, 'afterRequest']);
		return $promise;
	}
}