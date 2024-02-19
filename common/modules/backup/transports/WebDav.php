<?php
namespace common\modules\backup\transports;

use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\Application;

/**
 * Class WebDav
 * @package common\modules\backup\transports
 */
class WebDav extends Base {
	
	/**
	 * @var string
	 */
	public $url;
	
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
	
	public $timeOut = 90;
	
	public $appendTime = true;
	
	/**
	 * @var \creocoder\flysystem\WebDAVFilesystem
	 */
	private $client;
	
	private $file;
	
	/**
	 * @param array $config
	 */
	public function __construct($config = []) {
		parent::__construct(null);
		
		foreach ($config as $key => $value) {
			if ($this->hasProperty($key)) {
				$this->$key = $value;
			}
		}
		
		if ($this->url === null)
			throw new InvalidConfigException('The "url" property must be set.');
		
		if ($this->username === null)
			throw new InvalidConfigException('The "username" property must be set.');
		
		if ($this->password === null)
			throw new InvalidConfigException('The "password" property must be set.');
		
		if ($this->enable) {
			try {
				
				$this->client = Yii::createObject([
					'class' => 'creocoder\flysystem\WebDAVFilesystem',
					'baseUri' => $this->url,
					'userName' => $this->username,
					'password' => $this->password,
				]);
				
				if (!$this->client->has($this->dir))
					$this->client->createDir($this->dir);
				
				if ($this->appendTime) {
					$this->dir .= DIRECTORY_SEPARATOR.date('Y-m-d');
					if (!$this->client->has($this->dir))
						$this->client->createDir($this->dir);
				}
			} catch (ErrorException $e) {
				echo "Can not create folder. Make sure folder is existed".PHP_EOL;
			}
		}
	}
	
	/**
	 * @param string $file
	 *
	 * @return $this
	 */
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}
	
	/**
	 * @throws Exception
	 * @return bool
	 */
	public function push() {
		$hasError = true;
		$tryCount = 0;
		$lastError = false;
		while ($hasError && $tryCount != 9) {
			try {
				$info = pathinfo($this->file);
				
				$filename = $info['basename'];
				$destPath = $this->dir.DIRECTORY_SEPARATOR.$filename;
				
				// Write
				$stream = fopen($this->file, 'r+');
				$result = $this->client->writeStream($destPath, $stream);
				
				// Check size
				$fileSize = $this->client->getSize($destPath);
				$realFileSize = filesize($this->file);
				if ($fileSize !== $realFileSize) {
					$lastError = 'Size of file '.basename($this->file).' is '.$fileSize.' but real is '.$realFileSize;
					$tryCount++;
				}
				else {
					$hasError = false;
					$lastError = false;
					//$message = 'Create webdav backup .'.$destPath.' of size '.Yii::$app->formatter->asShortSize($fileSize);
					//Yii::$app->slack->info('CRON', $message);
				}
			} catch (Exception $e) {
				$tryCount ++;
				$lastError = mb_convert_encoding($e->getMessage(), 'utf8', 'cp1251');
			}
		}
		if ($hasError) {
			if (Yii::$app instanceof Application)
				throw new Exception($lastError);
			else {
				print_r($lastError);
			}
		}
		
		return $lastError;
	}
}