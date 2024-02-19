<?php
namespace common\modules\base\components\social;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class CloudMailRu extends Component
{
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
	public $dir;
	
	private $_api_version = 2;
	private $_token = '';
	private $_x_page_id = '';
	private $_build = '';
	private $_upload_url = '';
	private $_ch = '';
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		if ($this->username === null)
			throw new InvalidConfigException('The "username" property must be set.');
		
		if ($this->password === null)
			throw new InvalidConfigException('The "password" property must be set.');
		
		if (!$this->dir)
			$this->dir = Yii::getAlias('@runtime').DIRECTORY_SEPARATOR.'cloudmailru';
		if (!file_exists($this->dir))
			mkdir($this->dir);
		
		if (!$this->login())
			throw new InvalidConfigException('The "username" or "password" property is invalid.');
		
		parent::init();
	}
	
	public function __destruct() {
		$cookieFile = $this->dir.'/cookies.txt';
		if (file_exists($cookieFile))
			unlink($this->dir . '/cookies.txt');
	}
	
	/**
	 * Auth
	 * @return bool
	 */
	public function login() {
		$url = 'http://auth.mail.ru/cgi-bin/auth?lang=ru_RU&from=authpopup';
		
		$postData = [
			'page' => 'https://cloud.mail.ru/?from=promo',
			'FailPage' => '',
			'Domain' => 'mail.ru',
			'Login' => $this->username,
			'Password' => $this->password,
			'new_auth_form' => '1'
		];
		
		$this->_curlInit($url);
		$this->_curlPost($postData);
		$result = $this->_curlExec();
		if ($result !== 'error') {
			if ($this->_getToken())
				return true;
		}
		
		return false;
	}
	
	/**
	 * Get dir info
	 * @param $dir
	 *
	 * @return mixed|string
	 */
	public function getDir($dir) {
		$url = 'https://cloud.mail.ru/api/v2/folder';
		
		$postParams = [
			'api' => $this->_api_version,
			'sort' => '{"type":"name","order":"asc"}',
			'offset' => 0,
			'limit' => 500,
			'home' => $dir,
			'build' => $this->_build,
			'token' => $this->_token,
			'email' => $this->username.'@mail.ru',
			'x-email' => $this->username.'@mail.ru',
			'x-page-id' => $this->_x_page_id,
		];
		
		$this->_curlInit($url.'?'.http_build_query($postParams));
		$result = $this->_curlExec();
		
		return  ($result !== 'error') ? json_decode($result) : $result;
	}
	
	/**
	 * Create dir
	 * @param $dir
	 *
	 * @return mixed
	 */
	public function addDir($dir) {
		$url = 'https://cloud.mail.ru/api/v2/folder/add';
		$dir = str_replace('/', '%2F', $dir);
		
		$postParams = [
			'api' => 2,
			'home' => $dir,
			'token' => $this->_token,
			'build' => $this->_build,
			'conflict' => 'rename',
			'email' => $this->username.'@mail.ru',
			'x-email' => $this->username.'@mail.ru',
			'x-page-id' => $this->_x_page_id,
		];
		
		$this->_curlInit($url);
		$this->_curlPost($postParams);
		$result = $this->_curlExec();
		
		return ($result !== 'error') ? json_decode($result) : $result;
	}
	
	/**
	 * Get file
	 * @param $filePath
	 *
	 * @return bool|null|string
	 */
	public function getFile($filePath) {
		$response = $this->getNodes();
		if ($response !== 'error') {
			$nodes = array_column($response['body']['get'], 'url');
			$url = $nodes[mt_rand(0, count($nodes) - 1)].$filePath;
			$this->_curlInit($url);
			$response = $this->_curlExec();
			if ($response !== 'error')
				return $response;
		}
		return null;
	}
	
	/**
	 * Load file
	 * @param $fileName
	 * @param $dirCloud
	 *
	 * @return bool|mixed|string
	 */
	public function loadFile($fileName, $dirCloud, $conflict = 'rename') {
		$res = $this->_loadFileToCloud($fileName);
		if ($res !== 'error') {
			$result = $this->_addFileToCloud($res, $dirCloud, $conflict);
			var_dump($result);
			die;
			return ($result !== 'error') ? json_decode($result) : $result;
		}
		return $res;
	}
	
	/**
	 * Load file and publish
	 * @param $fileName
	 * @param $dirCloud
	 *
	 * @return string
	 */
	public function loadFileAhdPublish($fileName, $dirCloud) {
		$resultLoad = $this->loadFile($fileName, $dirCloud);
		if ($resultLoad !== 'error') {
			$resultPublish = $this->publishFile($resultLoad->body);
			if ($resultPublish !== 'error' && $resultPublish->status == '200') {
				return 'https://cloud.mail.ru/public/' . $resultPublish->body;
			}
		}
		return 'error';
	}
	
	/**
	 * Publish file
	 * @param $filePath
	 *
	 * @return bool|mixed
	 */
	public function publishFile($filePath) {
		$url = 'https://cloud.mail.ru/api/v2/file/publish';
		
		$postParams = [
			'api' => 2,
			'home' => $filePath,
			'token' => $this->_token,
			'build' => $this->_build,
			'email' => $this->username.'@mail.ru',
			'x-email' => $this->username.'@mail.ru',
			'x-page-id' => $this->_x_page_id,
		];
		
		$this->_curlInit($url);
		$this->_curlPost($postParams);
		$result = $this->_curlExec();
		
		return ($result !== 'error') ? json_decode($result) : $result;
	}
	
	/**
	 * Get nodes
	 * @return mixed|string
	 */
	public function getNodes() {
		$url = 'https://cloud.mail.ru/api/v2/dispatcher?token='.$this->_token;
		$this->_curlInit($url);
		$result = $this->_curlExec();
		return ($result !== 'error') ? json_decode($result, true) : $result;
	}
	
	/**
	 * @param $fileName
	 *
	 * @return array|mixed|string
	 */
	private function _loadFileToCloud($fileName) {
		$time = time().'0246';
		
		$url = $this->_upload_url.'?cloud_domain=1&x-email='.$this->username.'@mail.ru&fileapi'.$time;
		
		$postData = [
			'file' => curl_file_create($fileName),
		];
		
		$this->_curlInit($url);
		$this->_curlPost($postData);
		$result = $this->_curlExec();
		if ($result !== 'error') {
			$arr = explode(';', $result);
			if (strlen($arr[0]) == 40) {
				$arr[1] = intval($arr[1]);
				return $arr;
			}
		}
		return $result;
	}
	
	/**
	 * @param $arr
	 * @param $dirCloud
	 *
	 * @return bool|mixed|string
	 */
	private function _addFileToCloud($arr, $dirCloud, $conflict = 'rename') {
		$url = 'https://cloud.mail.ru/api/v2/file/add';
		
		$postData = [
			'api' => $this->_api_version,
			'build' => $this->_build,
			'token' => $this->_token,
			'conflict' => $conflict,
			'home' => $dirCloud,
			'hash' => $arr[0],
			'size' => $arr[1],
			'email' => $this->username.'@mail.ru',
			'x-email' => $this->username.'@mail.ru',
			'x-page-id' => $this->_x_page_id,
		];
		
		print_r($postData);
		
		$this->_curlInit($url);
		$this->_curlPost($postData, 'application/x-www-form-urlencoded');
		$result = $this->_curlExec();
		
		return ($result !== 'error') ? $result : false;
	}
	
	/**
	 * @param $dirCloud
	 *
	 * @return bool
	 */
	public function _removeFileFromCloud($dirCloud) {
		$url = 'https://cloud.mail.ru/api/v2/file/remove';
		
		$postData = [
			'api' => $this->_api_version,
			'build' => $this->_build,
			'token' => $this->_token,
			'home' => $dirCloud,
			'email' => $this->username.'@mail.ru',
			'x-email' => $this->username.'@mail.ru',
			'x-page-id' => $this->_x_page_id,
		];
		
		$this->_curlInit($url);
		$this->_curlPost($postData);
		$result = $this->_curlExec();
		
		return ($result !== 'error') ? $result : false;
	}
	
	/**
	 * Get token
	 * @return bool
	 */
	private function _getToken() {
		$url = 'https://cloud.mail.ru/?from=promo&from=authpopup';
		
		$this->_curlInit($url);
		$result = $this->_curlExec();
		if ($result !== 'error') {
			$token = self::_getTokenFromText($result);
			if ($token && strlen($token)) {
				$this->_token = $token;
				$this->_x_page_id = self::_getXPageIdFromText($result);
				$this->_build = self::_getBuildFromText($result);
				$this->_upload_url = self::_getUploadUrlFromText($result);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param $url
	 */
	private function _curlInit($url) {
		$this->_ch = curl_init();
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_REFERER, $url);
		curl_setopt($this->_ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17');
		curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->dir.'/cookies.txt');
		curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->dir.'/cookies.txt');
	}
	
	/**
	 * @param $postData
	 */
	private function _curlPost($postData, $contentType = 'multipart/form-data') {
		curl_setopt($this->_ch, CURLOPT_HTTPHEADER, [
			'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17',
			'Referer: https://cloud.mail.ru/home/',
			'Content-Type: '.$contentType,
		]);
		curl_setopt($this->_ch, CURLOPT_POST, true);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postData);
	}
	
	/**
	 * @return mixed|string
	 */
	private function _curlExec() {
		$result = curl_exec($this->_ch);
		$status = curl_errno($this->_ch);
		curl_close($this->_ch);
		if ($status == 0 && !empty($result)) {
			return $result;
		}
		return 'error';
	}
	
	/**
	 * @param $str
	 *
	 * @return bool|string
	 */
	private static function _getTokenFromText($str) {
		$start = strpos($str, '"csrf"');
		if ($start > 0) {
			$start = $start + 8;
			$str_out = substr($str, $start, 32);
			return $str_out;
		}
		return '';
	}
	
	/**
	 * @param $str
	 *
	 * @return bool|string
	 */
	private static function _getXPageIdFromText($str) {
		$start = strpos($str, '"x-page-id": "');
		if ($start > 0) {
			$start = $start + 14;
			$str_out = substr($str, $start, 11);
			return $str_out;
		}
		return '';
	}
	
	/**
	 * @param $str
	 *
	 * @return bool|string
	 */
	private static function _getBuildFromText($str) {
		$start = strpos($str, '"BUILD": "');
		if ($start > 0) {
			$start = $start + 10;
			
			$str_temp = substr($str, $start, 100);
			
			$end = strpos($str, '"');
			
			$str_out = substr($str_temp, 0, $end - 1);
			return $str_out;
		}
		return '';
	}
	
	/**
	 * @param $str
	 *
	 * @return bool|string
	 */
	private static function _getUploadUrlFromText($str) {
		$start = strpos($str, 'mail.ru/upload/"');
		if ($start > 0) {
			$start1 = $start - 50;
			$end1 = $start + 15;
			$lehgth = $end1 - $start1;
			$str_temp = substr($str, $start1, $lehgth);
			
			$start2 = strpos($str_temp, 'https://');
			$str_out = substr($str_temp, $start2, strlen($str_temp) - $start2);
			return $str_out;
		}
		return '';
	}
}