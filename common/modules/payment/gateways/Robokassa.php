<?php
namespace common\modules\payment\gateways;

use common\modules\payment\helpers\enum\Kind;
use Yii;

use common\modules\base\components\ArrayHelper;

use common\modules\payment\components\Request;
use common\modules\payment\components\Process;
use common\modules\payment\helpers\enum\Result;
use common\modules\payment\helpers\enum\State;
use common\modules\payment\exceptions\InvalidArgumentException;
use common\modules\payment\exceptions\NotFoundGatewayException;
use common\modules\payment\exceptions\SignatureMismatchRequestException;
use common\modules\payment\helpers\enum\Status;
use common\modules\payment\models\Payment;

/**
 * Class Robokassa
 * @package common\modules\payment\gateways
 */
class Robokassa extends Base
{
	/**
	 * @var string
	 */
	public $login;
	
	/**
	 * @var string
	 */
	public $password1;
	
	/**
	 * @var string
	 */
	public $password2;
	
	/**
	 * @var string
	 */
	public $testPassword1;
	
	/**
	 * @var string
	 */
	public $testPassword2;
	
	/**
	 * @var string
	 */
	public $url = 'https://auth.robokassa.ru/Merchant/Index.aspx';
	
	/**
	 * @return string
	 */
	static public function name() {
		return 'robokassa';
	}
	
	/**
	 * @param string $id
	 * @param integer|double $amount
	 * @param string $description
	 * @param array $params
	 *
	 * @return \common\modules\payment\components\Process
	 */
	public function start($id, $amount, $description, $params) {
		
		$email = null;
		if (isset($params['email'])) {
			$email = $params['email'];
			unset($params['email']);
		}
		
		// Additional params
		$shpParams = [];
		$shpSignature = '';
		foreach ($params as $key => $value) {
			if ($value) {
				$shpParams['Shp_'.$key] = $value;
				$shpSignature .= ':Shp_'.$key.'='.$value;
			}
		}
		
		$paymentParams = ArrayHelper::merge($shpParams, [
			'MrchLogin' => $this->login,
			'OutSum' => $amount,
			'InvId' => $id,
			'Desc' => $description,
			'SignatureValue' => md5($this->login.":".$amount.":".$id.":".$this->_getPassword1().$shpSignature),
			'IncCurrLabel' => $this->paymentMethod,
			'Culture' => 'ru',
			'Encoding' => 'utf-8',
			'IsTest' => $this->_getTestMode(),
		]);
		
		if ($email)
			$paymentParams['Email'] = $email;
		
		// Remote url
		$url = $this->url ?: ($this->testMode ? 'https://test.robokassa.ru/Index.aspx' : 'https://auth.robokassa.ru/Merchant/Index.aspx');
		
		return new Process([
			'state' => State::WAIT_VERIFICATION,
			'result' => Result::SUCCEED,
			'request' => new Request([
				'url' => $url,
				'params' => $paymentParams,
			])
		]);
	}
	
	/**
	 * @param Request $request
	 *
	 * @return Process
	 * @throws InvalidArgumentException
	 * @throws NotFoundGatewayException
	 * @throws SignatureMismatchRequestException
	 * @throws \yii\base\InvalidConfigException
	 */
	/**
	 * @param Request $request
	 *
	 * @return Process
	 * @throws InvalidArgumentException
	 * @throws NotFoundGatewayException
	 * @throws SignatureMismatchRequestException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function callback(Request $request) {
		
		// Check required params
		if (empty($request->params['InvId']) || empty($request->params['SignatureValue'])) {
			throw new InvalidArgumentException('Invalid request arguments. Need `InvId` and `SignatureValue`.');
		}
		
		// Find transaction model
		$transactionId = (int)$request->params['InvId'];
		
		$signParams = [
			$request->params['OutSum'],
			$transactionId,
			$this->_getPassword2(),
		];
		
		foreach ($request->params as $key => $val) {
			if (strpos($key, 'Shp') === 0)
				$signParams[] = $key.'='.$val;
		}
		
		// Generate hash sum
		$md5 = strtoupper(md5(implode(':', $signParams)));
		$remoteMD5 = strtoupper($request->params['SignatureValue']);
		
		// Check md5 hash
		if ($md5 !== $remoteMD5) {
			throw new SignatureMismatchRequestException();
		}
		
		/** @var Payment $model */
		$model = Payment::findById($transactionId);
		if (is_null($model) || $model->status != Status::WAIT)
			throw new NotFoundGatewayException();
		
		// Change status
		$model->status = Status::PAID;
		$model->save();
		
		// Send notifications
		$model->sendMessage();
		
		//if ($model->tax)
		
		//$modelWithdrawal = new Payment();
		//$modelWithdrawal->type = Kind::WITHDRAWAL;
		//$modelWithdrawal->user_id = $model->to_user_id;
		//$modelWithdrawal->to_user_id = 1;
		//$modelWithdrawal->price =
		
		// Send success result
		return new Process([
			'state' => State::COMPLETE,
			'result' => Result::SUCCEED,
			'responseText' => 'OK'.$transactionId,
		]);
	}
	
	/**
	 * @return bool
	 */
	private function _getTestMode() {
		return (YII_DEBUG && Yii::$app->user->getIsAdmin()) ? false : $this->testMode;
	}
	
	/**
	 * @return string
	 */
	private function _getPassword1() {
		return ($this->_getTestMode()) ? $this->testPassword1 : $this->password1;
	}
	
	/**
	 * @return string
	 */
	private function _getPassword2() {
		return ($this->_getTestMode()) ? $this->testPassword2 : $this->password2;
	}
	
}