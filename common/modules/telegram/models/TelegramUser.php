<?php
namespace common\modules\telegram\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\behaviors\ArrayFieldBehavior;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\user\models\User;
use common\modules\user\models\UserAccount;

use common\modules\telegram\models\query\TelegramUserQuery;

/**
 * This is the model class for table "{{%telegram_user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property array $params
 * @property integer $status
 * @property integer $lastvisit_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property UserAccount $userAccount
 */
class TelegramUser extends ActiveRecord
{
	/**
	 * Get module type
	 * @return int
	 */
	public function getModuleType() {
		return ModuleType::TELEGRAM_USER;
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%telegram_user}}';
    }
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => ArrayFieldBehavior::class,
				'attribute' => 'params',
			],
		]);
	}

    /**
     * @inheritdoc
     */
    public function rules()  {
        return [
            [['status', 'lastvisit_at', 'created_at', 'updated_at'], 'integer'],
            [['first_name'], 'required'],
            [['username', 'first_name', 'last_name'], 'string', 'max' => 255],
			[['params'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('telegram-user', 'field_id'),
            'username' => Yii::t('telegram-user', 'field_username'),
            'first_name' => Yii::t('telegram-user', 'field_first_name'),
            'last_name' => Yii::t('telegram-user', 'field_last_name'),
			'fullname' => Yii::t('telegram-user', 'field_fullname'),
			'telegram' => Yii::t('telegram-user', 'field_telegram'),
            'status' => Yii::t('telegram-user', 'field_status'),
			'lastvisit_at' => Yii::t('telegram-user', 'field_lastivist_at'),
            'created_at' => Yii::t('telegram-user', 'field_created_at'),
            'updated_at' => Yii::t('telegram-user', 'field_updated_at'),
        ];
    }
	
	/**
	 * @inheritdoc
	 * @return TelegramUserQuery the active query used by this AR class.
	 */
	public static function find() {
		return new TelegramUserQuery(get_called_class());
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id'])->via('userAccount');
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getUserAccount() {
		return $this->hasOne(UserAccount::class, ['client_id' => 'id'])->andWhere(['provider' => 'telegram']);
	}
    
	/**
	 * Get full name
	 * @return string
	 */
    public function getFullname() {
    	return $this->first_name.' '.$this->last_name;
	}
	
	public function getIsConnected() {
    	return $this->user;
	}
	
	/**
	 * Get telegram user ids by user ids
	 * @param $userId
	 *
	 * @return array
	 */
	static public function getUserIds($userId) {
		$userIds = (is_array($userId)) ? $userId : [$userId];
		
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
		
		$ids = [];
		$query = (new Query(null, $dependency))
			->cache()
			->select('client_id')
			->from(UserAccount::tableName())
			->where(['in', 'user_id', $userIds])
			->andWhere(['provider' => 'telegram']);
		foreach ($query->batch() as $rows) {
			$ids = ArrayHelper::merge($ids, (ArrayHelper::getColumn($rows, 'client_id')));
		}
		
		return $ids;
	}
}
