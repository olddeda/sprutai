<?php
namespace common\modules\base\components\social;

use yii\base\Component;
use InstagramScraper\Instagram as ServiceInstagram;

class Instagram extends Component
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
	 * @var object
	 */
	private $_service;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		if ($this->username === null)
			throw new InvalidConfigException('The "username" property must be set.');
		
		if ($this->password === null)
			throw new InvalidConfigException('The "password" property must be set.');
		
		// Init service and login
		$this->_service = ServiceInstagram::withCredentials($this->username, $this->password);
		$this->_service->login();
		
		parent::init();
	}
	
	/**
	 * @return object
	 */
	public function getService() {
		return $this->_service;
	}
	
	/**
	 * Get info
	 * @return \InstagramScraper\Model\Account
	 */
	public function getInfo() {
		return ServiceInstagram::getAccount($this->username);
	}
}