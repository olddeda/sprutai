<?php
namespace common\modules\payment\gateways;

use Yii;

use common\modules\base\components\ArrayHelper;
use common\modules\base\components\Debug;

use common\modules\payment\components\Request;
use common\modules\payment\components\Process;
use common\modules\payment\helpers\enum\Result;
use common\modules\payment\helpers\enum\State;
use common\modules\payment\exceptions\GatewayException;
use common\modules\payment\exceptions\InvalidArgumentException;
use common\modules\payment\exceptions\NotFoundGatewayException;
use common\modules\payment\exceptions\SignatureMismatchRequestException;
use common\modules\payment\helpers\enum\Status;
use common\modules\payment\models\Payment;

/**
 * Class Megakassa
 * @package common\modules\payment\gateways
 */
class Megakassa extends Base
{
	/**
	 * @var string
	 */
	public $name = 'megakassa';
	
	/**
	 * @var integer
	 */
	public $shopId;
	
	/**
	 * @var string
	 */
	public $secretKey;
	
	/**
	 * @var string
	 */
	public $currency = 'RUB';
	
	/**
	 * @var string
	 */
	public $serverUrl = 'https://megakassa.ru/merchant/';
	
	/**
	 * @var string
	 */
	public $serverIP = '5.196.121.217';
	
	/**
	 * @return string
	 */
	static public function name() {
		return 'megakassa';
	}
	
	/**
	 * @param int $id
	 * @param float|int $amount
	 * @param string $description
	 * @param array $params
	 *
	 * @return Process|void
	 */
	public function start($id, $amount, $description, $params) {
		
		$fields = [
			'shop_id' => $this->shopId,
			'amount' => $amount,
			'currency' => $this->currency,
			'description' => $description,
			'order_id' => $id,
			'method_id' => '',
			'client_email' => '',
			'debug' => $this->testMode,
			'secret_key' => $this->secretKey,
		];
		
		$signature = md5($this->secretKey.md5(join(':', array_values($fields))));
		
		$paymentParams = ArrayHelper::merge($fields, [
			'signature' => $signature,
			'language' => 'ru',
		]);
		
		return new Process([
			'state' => State::WAIT_VERIFICATION,
			'result' => Result::SUCCEED,
			'request' => new Request([
				'url' => $this->serverUrl,
				'params' => $paymentParams,
			])
		]);
	}
	
	/**
	 * @param Request $request
	 *
	 * @return Process
	 * @throws GatewayException
	 * @throws InvalidArgumentException
	 * @throws NotFoundGatewayException
	 * @throws SignatureMismatchRequestException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function callback(Request $request) {
		
		// Check server IP
		$isCheckedIP = false;
		foreach([
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'HTTP_CLIENT_IP',
			'REMOTE_ADDR'
		] as $param) {
			if(!empty($_SERVER[$param]) && $_SERVER[$param] === $this->serverIP) {
				$isCheckedIP = true;
				break;
			}
		}
		
		if (!$isCheckedIP)
			throw new GatewayException('Is checked IP server fail `' . __CLASS__ . '`.');
		
		// Check fields
		foreach([
			'uid',
			'amount',
			'amount_shop',
			'amount_client',
			'currency',
			'order_id',
			'payment_method_title',
			'creation_time',
			'client_email',
			'status',
			'signature'
		] as $field) {
			if (empty($_REQUEST[$field])) {
				//Yii::$app->notification->
				throw new InvalidArgumentException('Check required field '.$field.' failed `'.__CLASS__.'`.');
			}
		}
		
		// Collect params
		$uid					= (int)$_REQUEST['uid'];
		$amount					= (double)$_REQUEST['amount'];
		$amountShop				= (double)$_REQUEST['amount_shop'];
		$amountClient			= (double)$_REQUEST['amount_client'];
		$currency				= $_REQUEST['currency'];
		$orderId				= $_REQUEST['order_id'];
		$paymentMethodId		= (int)$_REQUEST['payment_method_id'];
		$paymentMethodTitle		= $_REQUEST['payment_method_title'];
		$creationTime			= $_REQUEST['creation_time'];
		$paymentTime			= $_REQUEST['payment_time'];
		$clientEmail			= $_REQUEST['client_email'];
		$status					= $_REQUEST['status'];
		$debug					= (!empty($_REQUEST['debug'])) ? '1' : '0';
		$signature				= $_REQUEST['signature'];
		
		// Check currency value
		if (!in_array($currency, array('RUB', 'USD', 'EUR'), true))
			throw new InvalidArgumentException('Error currency value '.$currency.' `' . __CLASS__ . '`.');
		
		// Check status value
		if (!in_array($status, array('success', 'fail'), true))
			throw new InvalidArgumentException('Error status value '.$status.' `' . __CLASS__ . '`.');
		
		// Check signature format
		if (!preg_match('/^[0-9a-f]{32}$/', $signature))
			throw new InvalidArgumentException('Error signature format '.$status.' `' . __CLASS__ . '`.');
		
		// Check signature valid
		$signatureCalc = md5(join(':', [
			$uid, $amount, $amountShop, $amountClient, $currency, $orderId,
			$paymentMethodId, $paymentMethodTitle, $creationTime, $paymentTime,
			$clientEmail, $status, $debug, $this->secretKey
		]));
		if ($signatureCalc !== $signature)
			throw new SignatureMismatchRequestException('Error signature validate '.$signatureCalc.' != '.$signature.' `' . __CLASS__ . '`.');
		
		
		/** @var Payment $model */
		$model = Payment::findById($orderId);
		if (is_null($model))
			throw new NotFoundGatewayException();
		
		// Change status
		$model->status = ($status == 'success') ? Status::PAID : Status::FAILED;
		$model->save();
		
		// Send notifications
		$model->sendMessage();
		
		// Send success result
		return new Process([
			'state' => State::COMPLETE,
			'result' => Result::SUCCEED,
			'responseText' => 'ok',
		]);
	}
	
	
}