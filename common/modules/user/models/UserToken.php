<?php

namespace common\modules\user\models;

use Yii;
use yii\helpers\Url;

use common\modules\base\components\ActiveRecord;

/**
 * UserToken Active Record model.
 *
 * @property integer $user_id
 * @property integer $type
 * @property string $code
 * @property string $url
 * @property bool $isExpired
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property User $user
 *
 */
class UserToken extends ActiveRecord
{
	const TYPE_CONFIRMATION 	 	= 0;
	const TYPE_RECOVERY 		 	= 1;
	const TYPE_CONFIRM_NEW_EMAIL 	= 2;
	const TYPE_CONFIRM_OLD_EMAIL 	= 3;
	const TYPE_API 					= 4;
	const TYPE_RECOVERY_MOBILE 		= 5;

	/**
	 * @var \common\modules\user\Module
	 */
	protected $module;

	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->module = Yii::$app->getModule('user');
	}

	/**
	 * @inheritdoc
	 */
	public static function getDb() {
		return Yii::$app->get('db');
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%user_token}}';
	}

	/**
	 * @inheritdoc
	 */
	public static function primaryKey() {
		return ['user_id', 'code', 'type'];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
	}

	/**
	 * @return string
	 */
	public function getUrl($scheme = true) {
		switch ($this->type) {
			case self::TYPE_CONFIRMATION:
				$route = '/user/signup/confirm';
				break;
			case self::TYPE_RECOVERY:
				$route = '/user/forgot/reset';
				break;
			case self::TYPE_CONFIRM_NEW_EMAIL:
			case self::TYPE_CONFIRM_OLD_EMAIL:
				$route = '/user/settings/confirm';
				break;
			default:
				throw new \RuntimeException();
		}

		return Url::to([$route, 'id' => $this->user_id, 'code' => $this->code], $scheme);
	}

	/**
	 * @return bool Whether token has expired.
	 */
	public function getIsExpired() {
		switch ($this->type) {
			case self::TYPE_CONFIRMATION:
			case self::TYPE_CONFIRM_NEW_EMAIL:
			case self::TYPE_CONFIRM_OLD_EMAIL:
				$expirationTime = $this->module->confirmWithin;
				break;
			case self::TYPE_RECOVERY:
			case self::TYPE_RECOVERY_MOBILE:
				$expirationTime = $this->module->recoverWithin;
				break;
			case self::TYPE_API:
				$expirationTime = $this->module->apiWithin;
				break;
			default:
				throw new \RuntimeException();
		}

		return ($this->created_at + $expirationTime) < time();
	}

	/**
	 * Generate random numbers
	 * @param int $length
	 *
	 * @return int
	 */
	public static function generateNumbers($length = 4) {
		$number = '';
		for ($i = 0; $i < $length; $i++)
			$number .= mt_rand(0, $length);
		return $number;
	}

	/**
	 * Delete model
	 * @param bool|true $useStatus
	 */
	public function delete($useStatus = true) {
		$useStatus = false;
		parent::delete($useStatus);
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if ($insert) {
			$code = Yii::$app->security->generateRandomString();
			if ($this->type == self::TYPE_RECOVERY_MOBILE)
				$code = self::generateNumbers();

			static::deleteAll([
				'user_id' => $this->user_id,
				'type' => $this->type
			]);
			$this->setAttribute('created_at', time());
			$this->setAttribute('code', $code);
		}
		else {
			if (in_array($this->type, [self::TYPE_API]))
				$this->setAttribute('created_at', time());
		}
		return parent::beforeSave($insert);
	}
}
