<?php

namespace common\modules\backup;

use common\modules\backup\transports\WebDav;
use Yii;
use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;
use yii\helpers\ArrayHelper;

use common\modules\backup\transports\Ftp;
use common\modules\backup\transports\Mail;

/**
 * @property Mail $mail
 * @property Ftp $ftp
 * @property WebDav $webdav
 */
class Module extends BaseModule {
	
	const TYPE_DB = 'db';
	const TYPE_FOLDER = 'folder';
	
	public $backupPath = '@runtime/backup';
	
	public $transport = [];
	public $backup = [];
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		parent::init();
		
		$this->backupPath = Yii::getAlias($this->backupPath);
		if (!file_exists($this->backupPath)) {
			mkdir($this->backupPath, 0777, true);
		}
		
		$this->backup = ArrayHelper::merge([
			'db' => [
				'enable' => true,
				'data' => [
					'db' => [
						'tables' => [],
						'except' => [],
					],
				],
			],
			'folder' => [
				'enable' => false,
				'data' => [],
			],
		], $this->backup);
		
		$this->transport = ArrayHelper::merge([
			'mail' => [
				'class' => '\common\modules\backup\transports\Mail',
				'enable' => true,
				'fromEmail' => 'support@email.com',
				'toEmail' => 'backup@email.com',
			],
			'ftp' => [
				'class' => '\common\modules\backup\transports\Ftp',
				'enable' => false,
				'host' => '',
				'port' => 21,
				'ssl' => false,
				'user' => '',
				'pass' => '',
				'dir' => '',
				'timeOut' => 90,
				'appendTime' => true,
			],
			'webdav' => [
				'class' => '\common\modules\transports\WebDav',
				'enable' => false,
				'url' => '',
				'username' => '',
				'password' => '',
				'dir' => '',
			],
		], $this->transport);
	}
	
	/**
	 * @return bool
	 */
	public function backupDbEnable() {
		return $this->backup['db']['enable'];
	}
	
	/**
	 * @return array
	 */
	public function backupDbData() {
		return array_unique($this->backup['db']['data']);
	}
	
	/**
	 * @return bool
	 */
	public function backupFolderEnable() {
		return $this->backup['folder']['enable'];
	}
	
	/**
	 * @return array
	 */
	public function backupFolderData() {
		return array_unique($this->backup['folder']['data']);
	}
	
	/**
	 * @return Mail
	 */
	public function getMail() {
		$mailClass = $this->transport['mail']['class'];
		return new $mailClass($this->transport['mail']);
	}
	
	/**
	 * @return Ftp
	 */
	public function getFtp() {
		$ftpClass = $this->transport['ftp']['class'];
		return new $ftpClass($this->transport['ftp']);
	}
	
	/**
	 * @return WebDav
	 */
	public function getWebdav() {
		$webDavClass = $this->transport['webdav']['class'];
		return new $webDavClass($this->transport['webdav']);
	}
}