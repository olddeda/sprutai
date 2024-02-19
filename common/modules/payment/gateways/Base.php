<?php
namespace common\modules\payment\gateways;

use yii\base\BaseObject;
use yii\log\Logger;

use common\modules\payment\Module;
use common\modules\payment\components\Request;

/**
 * Class Base
 * @package common\modules\payment\gateways
 */
abstract class Base extends BaseObject
{
    /**
     * Флаг, отображающий включена ли платёжный шлюз.
     * @var boolean
     */
    public $enable = true;

    /**
     * Способ оплаты. Поле актуально только для платёжных интеграторов, где есть выбор способа оплаты.
     * @var string
     */
    public $paymentMethod;

    /**
     * Флаг, отображающий включен ли платёжный шлюз для реальных транзакций.
     * По-умолчанию включен режим разработчика.
     * @var boolean
     */
    public $testMode = true;

    /**
     * Имя платёжного шлюза, одно из значений enum GatewayName
     * @var string
     */
    public $name;

    /**
     *
     * @var Module
     */
    public $module;
	
	/**
	 * @return string
	 */
	abstract static public function name();

    /**
     * @param int $id
     * @param integer|double $amount
     * @param string $description
     * @param array $params
     * @return \common\modules\payment\components\Process
     */
    abstract public function start($id, $amount, $description, $params);

    /**
     * @param Request $request
     * @return \common\modules\payment\components\Process
     */
    abstract public function callback(Request $request);

    /**
     * Адрес магазина/сайта
     * @return string
     */
    public function getSiteUrl() {
        return self::appendToUrl($this->module->siteUrl, 'gatewayName=' . $this->name);
    }

    /**
     * Адрес, по которому должна направить пользователя платёжная система при успешной оплате
     * @return string
     */
    public function getSuccessUrl() {
        return self::appendToUrl($this->module->successUrl, 'gatewayName=' . $this->name);
    }

    /**
     * Адрес, по которому должна направить пользователя платёжная система при неудачной оплате
     * @return string
     */
    public function getFailureUrl() {
        return self::appendToUrl($this->module->failureUrl, 'gatewayName=' . $this->name);
    }
	
	/**
	 * @param $url
	 * @param $query
	 *
	 * @return string
	 */
	protected static function appendToUrl($url, $query) {
		return $url.(strpos($url, '?') === false ? '?' : '&').$query;
	}

    /**
     * @param $message
     * @param integer $level
     * @param null $transactionId
     * @param array $stateData
     */
    protected function log($message, $level = Logger::LEVEL_INFO, $transactionId = null, $stateData = array()) {
        $this->module->log($message, $level, $transactionId, $stateData);
    }
	
	/**
	 * @param string|integer $id
	 * @param array $data
	 */
	protected function setStateData($id, $data = []) {
		$this->module->stateSaver->set($this->name.'_'.(string)$id, $data);
	}

    /**
     * @param $id
     * @return mixed
     */
    protected function getStateData($id) {
        return $this->module->stateSaver->get($id);
    }
	
	/**
	 * @param $url
	 * @param array $params
	 * @param array $headers
	 *
	 * @return mixed
	 */
    protected function httpSend($url, $params = [], $headers = []) {
        return $this->module->httpSend($url, $params, $headers);
    }
	
	/**
	 * @param $id
	 *
	 * @return null
	 */
    protected function findOrderStateById($id) {
        $className = $this->module->orderClassName;
        return class_exists($className) ? $className::findOrderStateById($id) : null;
    }
}
