<?php
namespace common\modules\base\components\social;

use yii\base\Component;

use Yandex\Metrica\Management\ManagementClient;
use Yandex\Metrica\Stat\StatClient;

class YandexMetrika extends Component
{
	/**
	 * @var string
	 */
	public $token;
	
	/**
	 * @var integer
	 */
	public $counter_id;
	
	/**
	 * @var \Yandex\Metrica\Management\ManagementClient
	 */
	private $_client;
	
	private $_stat_client;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		if ($this->token === null)
			throw new InvalidConfigException('The "token" property must be set.');
		
		parent::init();
	}
	
	/**
	 * @return \Yandex\Metrica\Management\ManagementClient
	 */
	public function getClient() {
		if (is_null($this->_client))
			$this->_client = new ManagementClient($this->token);
		return $this->_client;
	}
	
	/**
	 * @return \Yandex\Metrica\Stat\StatClient
	 */
	public function getStatClient() {
		if (is_null($this->_stat_client))
			$this->_stat_client = new StatClient($this->token);
		return $this->_stat_client;
	}
}