<?php

namespace common\modules\user\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\redis\ActiveRecord;

use common\modules\user\models\query\UserLogQuery;

/**
 * Class UserLog
 *
 * @package app\models
 *
 * @property integer $id
 * @property string $user_id
 * @property string $ip
 * @property string $user_agent
 * @property integer $visit
 */
class UserLog extends ActiveRecord
{

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public static function primaryKey() {
		return ['id'];
	}

	/**
	 * Returns the list of all attribute names of the model.
	 * This method must be overridden by child classes to define available attributes.
	 * @return array list of attribute names.
	 */
	public function attributes() {
		return ['id', 'user_id', 'user_agent', 'ip', 'visit'];
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function rules() {
		return [
			[['id', 'user_id', 'visit'], 'integer'],
			[['user_agent', 'ip'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('user-log', 'field_id'),
			'user_id' => Yii::t('user-log', 'field_user_id'),
			'user_agent' => Yii::t('user-log', 'field_user_agent'),
			'ip' => Yii::t('user-log', 'field_ip'),
			'visit' => Yii::t('user-log', 'field_visit'),
		];
	}

	/**
	 * @inheritdoc
	 * @return \common\modules\user\models\query\UserLogQuery the active query used by this AR class.
	 */
	public static function find() {
		return new UserLogQuery(get_called_class());
	}

	/**
	 * @return \yii\db\ActiveQueryInterface
	 */
	public function getActions() {
		//return self::hasMany(ActionLog::className(), ['user_id' => 'id']);
	}

	/**
	 * Find log by user id
	 * @param $userId
	 *
	 * @return UserLog
	 */
	public static function findByUserId($userId) {
		return self::findOne(['user_id' => $userId]);
	}

	/**
	 * Find log by ip address
	 * @param $ip
	 *
	 * @return UserLog
	 */
	public static function findByIp($ip) {
		return self::findOne(['ip' => $ip]);
	}
}