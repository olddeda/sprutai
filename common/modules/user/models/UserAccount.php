<?php
namespace common\modules\user\models;

use common\modules\base\helpers\enum\Status;
use Yii;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\helpers\Url;

use common\modules\base\components\ActiveRecord;

use common\modules\user\Module;
use common\modules\user\Finder;
use common\modules\user\clients\ClientInterface;
use common\modules\user\models\query\UserAccountQuery;

/**
 * @property integer $id Id
 * @property integer $user_id User id, null if account is not bind to user
 * @property string  $provider Name of service
 * @property string  $client_id Account id
 * @property string  $data Account properties returned by social network (json encoded)
 * @property string  $decodedData Json-decoded properties
 * @property string  $code
 * @property string  $email
 * @property string  $username
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user User that this account is connected for.
 */
class UserAccount extends ActiveRecord
{
    /**
	 * @var Module
	 */
    protected $module;

    /**
	 * @var Finder
	 */
    protected static $finder;

    /** @var */
    private $_data;

    /**
	 * @inheritdoc
	 */
    public function init() {
        $this->module = Yii::$app->getModule('user');
    }
    
    /**
	 * @inheritdoc
	 */
    public static function tableName() {
        return '{{%user_account}}';
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @return bool Whether this social account is connected to user.
     */
    public function getIsConnected() {
        return $this->user_id != null;
    }

    /**
     * @return mixed Json decoded properties.
     */
    public function getDecodedData() {
        if ($this->_data == null) {
            $this->_data = json_decode($this->data);
        }
        return $this->_data;
    }

    /**
     * Returns connect url.
     * @return string
     */
    public function getConnectUrl() {
        $code = Yii::$app->security->generateRandomString();
        $this->updateAttributes(['code' => md5($code)]);
        return Url::to(['/user/signup/connect', 'code' => $code]);
    }

	/**
	 * Connect user
	 * @param User $user
	 *
	 * @return int
	 */
    public function connect(User $user) {
        return $this->updateAttributes([
            'username' => null,
            'email' => null,
            'code' => null,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @return UserAccountQuery
     */
    public static function find() {
        return Yii::createObject(UserAccountQuery::class, [get_called_class()]);
    }

	/**
	 * Create account
	 * @param BaseClientInterface $client
	 *
	 * @return UserAccount
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function create(BaseClientInterface $client) {

		/** @var UserAccount $account */
		$account = Yii::createObject([
			'class' => static::className(),
			'provider' => $client->getId(),
			'client_id' => $client->getUserAttributes()['id'],
			'data' => json_encode($client->getUserAttributes()),
		]);

		if ($client instanceof ClientInterface) {
			$account->setAttributes([
				'username' => $client->getUsername(),
				'email' => $client->getEmail(),
			], false);
		}

		if (($user = static::fetchUser($account)) instanceof User) {
			$account->user_id = $user->id;
		}

		$account->save(false);

		return $account;
	}

    /**
     * Tries to find an account and then connect that account with current user.
     *
     * @param BaseClientInterface $client
     */
    public static function connectWithUser(BaseClientInterface $client) {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'message_something_went_wrong'));
            return;
        }

        $account = static::fetchAccount($client);

        if ($account->user === null) {
            $account->link('user', Yii::$app->user->identity);
            Yii::$app->session->setFlash('success', Yii::t('user', 'message_your_account_has_been_connected'));
        }
		else {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'message_this_account_has_already_been_connected_to_another_user'));
        }
    }

    /**
     * Tries to find account, otherwise creates new account.
     *
     * @param BaseClientInterface $client
     *
     * @return UserAccount
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchAccount(BaseClientInterface $client) {
        $account = static::getFinder()->findAccount()->byClient($client)->one();

        if (null === $account) {
            $account = Yii::createObject([
                'class' => static::className(),
                'provider' => $client->getId(),
                'client_id' => $client->getUserAttributes()['id'],
                'data' => json_encode($client->getUserAttributes()),
            ]);
            $account->save(false);
        }

        return $account;
    }

    /**
     * Tries to find user or create a new one.
     *
     * @param UserAccount $account
     *
     * @return User|bool False when can't create user.
     */
    protected static function fetchUser(UserAccount $account) {
        $user = static::getFinder()->findUserByEmail($account->email);

        if (null !== $user) {
            return $user;
        }

        $user = Yii::createObject([
            'class' => User::class,
            'scenario' => 'connect',
            'username' => $account->username,
            'email' => $account->email,
	        
        ]);

        if (!$user->validate(['email'])) {
            $account->email = null;
        }

        if (!$user->validate(['username'])) {
            $account->username = null;
        }

        return $user->create(false) ? $user : false;
    }

    /**
     * @return Finder
     */
    protected static function getFinder() {
        if (static::$finder === null) {
            static::$finder = Yii::$container->get(Finder::class);
        }
        return static::$finder;
    }
    
    static public function generateCode($client) {
    	$code = null;
    	while (is_null($code)) {
    		$randomCode = strtoupper(Yii::$app->getSecurity()->generateRandomString(5));
    		if (!self::find()->where('provider = :provider AND code = :code',  [
    			':provider' => $client,
				':code' => $randomCode,
			])->count()) {
    			$code = $randomCode;
			}
		}
		return $code;
	}
}
