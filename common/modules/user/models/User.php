<?php
namespace common\modules\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\Query;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

use common\traits\UserTrait;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\behaviors\ArrayFieldBehavior;
use common\modules\base\helpers\enum\Status;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\models\MediaImage;
use common\modules\media\helpers\enum\Type;
use common\modules\media\helpers\enum\Mode;

use common\modules\vote\behaviors\VoteBehavior;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;
use common\modules\content\models\ContentAuthorStat;
use common\modules\content\helpers\enum\Status as ContentStatus;
use common\modules\content\helpers\enum\Type as ContentType;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;

use common\modules\user\Module;
use common\modules\user\Finder;
use common\modules\user\Mailer;
use common\modules\user\helpers\Password;
use common\modules\user\models\UserActivity;
use common\modules\user\models\query\UserQuery;
use common\modules\user\models\query\UserActivityQuery;

/**
 * User ActiveRecord model.
 *
 * @property bool $isAdmin
 * @property bool $isBlocked
 * @property bool $isConfirmed
 *
 * Database fields:
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $unconfirmed_email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $data
 * @property integer $registration_ip
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $confirmed_at
 * @property integer $lastvisit_at
 * @property integer $blocked_at
 * @property integer $deleted_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property integer $flags
 *
 * Defined relations:
 * @property \common\modules\user\models\UserProfile $profile
 * @property \common\modules\user\models\UserAddress $address
 * @property \common\modules\user\models\UserAddress[] $addresses
 * @property \common\modules\user\models\UserSubscribe $subscribe
 * @property \common\modules\user\models\UserToken $token
 * @property \common\modules\user\models\UserToken[] $tokens
 * @property \common\modules\user\models\UserAccount[] $accounts
 * @property \common\modules\user\models\UserAccount $telegram
 * @property \common\modules\user\models\UserAccount $github
 * @property \common\modules\user\models\UserActivity[] $activities
 * @property \common\modules\content\models\Content[] $contents
 * @property \common\modules\content\models\ContentAuthorStat $contentsStat
 * @property \common\modules\content\models\Article[] $contentsArticles
 * @property \common\modules\content\models\News[] $contentsNews
 * @property \common\modules\content\models\Blog[] $contentsBlogs
 * @property \common\modules\project\models\Project[] $contentsProjects
 * @property \common\modules\plugin\models\Plugin[] $contentsPlugins
 * @property \common\modules\tag\models\Tag[] $tags
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
	//use UserTrait;

	// Events names
	const BEFORE_CREATE 	= 'beforeCreate';
	const AFTER_CREATE 		= 'afterCreate';
	const BEFORE_SIGNUP 	= 'beforeSignup';
	const AFTER_SIGNUP 		= 'afterSignup';

	// Scenarios
	const SCENARIO_SIGNUP	= 'signup';
	const SCENARIO_CONNECT	= 'connect';
	const SCENARIO_CREATE	= 'create';
	const SCENARIO_UPDATE	= 'update';
	const SCENARIO_SETTINGS	= 'settings';

	// following constants are used on secured email changing process
	const OLD_EMAIL_CONFIRMED = 0b1;
	const NEW_EMAIL_CONFIRMED = 0b10;

	/**
	 * @var string Plain password. Used for model validation.
	 */
	public $password;

	/**
	 * @var \common\modules\user\Module
	 */
	protected $module;

	/**
	 * @var \common\modules\user\Mailer
	 */
	protected $mailer;

	/**
	 * @var \common\modules\user\Finder
	 */
	protected $finder;

	/**
	 * @var \common\modules\user\models\UserProfile|null
	 */
	private $_profile;
	
	/**
	 * @var \common\modules\user\models\UserSubscribe|null
	 */
	private $_subscribe;

	/**
	 * @var string Default username regexp
	 */
	public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->finder = Yii::$container->get(Finder::class);
		$this->mailer = Yii::$container->get(Mailer::class);
		$this->module = Yii::$app->getModule('user');
		parent::init();
	}
	
	/**
	 * Get module type
	 * @return int
	 */
	public function getModuleType() {
		return ModuleType::USER;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'avatar',
				'type' => Type::IMAGE,
 			],
			[
				'class' => MediaBehavior::class,
				'attribute' => 'background',
				'type' => Type::IMAGE,
			],
			[
				'class' => VoteBehavior::class,
			],
			[
				'class' => ArrayFieldBehavior::class,
				'attribute' => 'data',
			],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		return [
			self::SCENARIO_DEFAULT => ['data'],
			self::SCENARIO_SIGNUP => ['username', 'email', 'password'],
			self::SCENARIO_CONNECT => ['username', 'email'],
			self::SCENARIO_CREATE => ['username', 'email', 'password'],
			self::SCENARIO_UPDATE => ['username', 'email', 'password'],
			self::SCENARIO_SETTINGS => ['username', 'email', 'password'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
            
            [['lastvisit_at'], 'integer'],

			// username rules
			'usernameRequired' => ['username', 'required', 'on' => [self::SCENARIO_SIGNUP, self::SCENARIO_CREATE, self::SCENARIO_CONNECT, self::SCENARIO_UPDATE]],
			'usernameMatch' => ['username', 'match', 'pattern' => static::$usernameRegexp],
			'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
			'usernameUnique' => ['username', 'unique', 'targetAttribute' => ['username'], 'message' => Yii::t('user', 'error_this_username_has_already_been_taken')],
			'usernameTrim' => ['username', 'trim'],

			// email rules
			'emailRequired' => ['email', 'required', 'on' => [self::SCENARIO_SIGNUP, self::SCENARIO_CREATE, self::SCENARIO_CONNECT, self::SCENARIO_UPDATE]],
			'emailPattern' => ['email', 'email'],
			'emailLength' => ['email', 'string', 'max' => 255],
			'emailUnique' => ['email', 'unique', 'targetAttribute' => ['email'], 'message' => Yii::t('user', 'error_this_email_address_has_already_been_taken')],
			'emailTrim' => ['email', 'trim'],

			// password rules
			'passwordRequired' => ['password', 'required', 'on' => [self::SCENARIO_SIGNUP]],
			'passwordLength' => ['password', 'string', 'min' => 6, 'on' => [self::SCENARIO_SIGNUP, self::SCENARIO_CREATE]],
			
			[['username', 'email'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
			
			[['data'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('user', 'field_id'),
			'username' => Yii::t('user', 'field_username'),
			'email' => Yii::t('user', 'field_email'),
			'registration_ip' => Yii::t('user', 'field_registration_ip'),
			'unconfirmed_email' => Yii::t('user', 'field_unconfirmed_email'),
			'password' => Yii::t('user', 'field_password'),
			'created_at' => Yii::t('user', 'field_created_at'),
			'confirmed_at' => Yii::t('user', 'field_confirmed_at'),
			'blocked_at' => Yii::t('user', 'field_blocked_at'),
			'deleted_at' => Yii::t('user', 'field_deleted_at'),
			'fio' => Yii::t('user', 'field_fio'),
			'phone' => Yii::t('user', 'field_phone'),
		];
	}
	
	/**
	 * @return UserQuery|object|\yii\db\ActiveQuery
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function find() {
		return Yii::createObject(UserQuery::className(), [get_called_class()]);
	}

	/**
	 * Find identity
	 *
	 * @param int|string $id
	 *
	 * @return null|static
	 */
	public static function findIdentity($id) {
		return static::findOne($id);
	}

	/**
	 * Find identity by access token
	 *
	 * @inheritdoc
	 *
	 * @param mixed $token
	 * @param null $type
	 *
	 * @throws NotSupportedException
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		return static::findOne(['access_token' => $token]);
	}
	
	
	/**
	 * Prepare query
	 * @param $query
	 */
	public static function prepareQuery($query) {
		if (Yii::$app instanceof yii\web\Application) {
			$query->votes();
		}
	}
	
	/**
	 * Get profile
	 * @return \yii\db\ActiveQuery
	 */
	public function getProfile() {
		return $this->hasOne($this->module->modelMap['UserProfile'], ['user_id' => 'id']);
	}
	
	/**
	 * Get profile
	 * @return \yii\db\ActiveQuery
	 */
	public function getSubscribe() {
		return $this->hasOne($this->module->modelMap['UserSubscribe'], ['user_id' => 'id']);
	}

	/**
	 * Set user profile
	 * @param UserProfile $profile
	 */
	public function setProfile(UserProfile $profile) {
		$this->_profile = $profile;
	}

	/**
	 * Get accounts
	 * @return UserAccount[] Connected accounts ($provider => $account)
	 */
	public function getAccounts() {
		$connected = [];
		$accounts = $this->hasMany($this->module->modelMap['UserAccount'], ['user_id' => 'id'])->all();

		/** @var Account $account */
		foreach ($accounts as $account) {
			$connected[$account->provider] = $account;
		}
		
		return $connected;
	}

	/**
	 * Get tokens relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getTokens() {
		return $this->hasMany($this->module->modelMap['UserToken'], ['user_id' => 'id']);
	}
	
	/**
	 * Get address relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getAddress() {
		return $this->hasOne($this->module->modelMap['UserAddress'], ['user_id' => 'id'])->andOnCondition(['is_primary' => true]);
	}
	
	/**
	 * Get addresses relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getAddresses() {
		return $this->hasMany($this->module->modelMap['UserAddress'], ['user_id' => 'id']);
	}
	
	/**
	 * Get address relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getTelegram() {
		return $this->hasOne($this->module->modelMap['UserAccount'], ['user_id' => 'id'])->alias('telegram')->andOnCondition(['telegram.provider' => 'telegram']);
	}
	
	/**
	 * Get address relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getGithub() {
		return $this->hasOne($this->module->modelMap['UserAccount'], ['user_id' => 'id'])->alias('github')->andOnCondition(['github.provider' => 'github']);
	}
	
	/**
	 * Get content relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContents() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			Content::tableName().'.status' => ContentStatus::ENABLED,
		])->onCondition([
			'in',
			Content::tableName().'.type',
			[
				ContentType::ARTICLE,
				ContentType::NEWS,
				ContentType::BLOG,
			]
		])->joinWith([
			//'tags',
			//'paymentTypes ptC',
		])->where([]);
	}
	
	/**
	 * Get content stat relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContentsStat() {
		return $this->hasOne(ContentAuthorStat::class, ['author_id' => 'id']);
	}
	
	/**
	 * Get content aricles relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContentsArticles() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			'articles.status' => ContentStatus::ENABLED,
			'articles.type' => ContentType::ARTICLE,
		])->alias('articles')->where([]);
	}
	
	/**
	 * Get content news relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContentsNews() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			'news.status' => ContentStatus::ENABLED,
			'news.type' => ContentType::NEWS,
		])->alias('news')->where([]);
	}
	
	/**
	 * Get content blogs relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContentsBlogs() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			'blogs.status' => ContentStatus::ENABLED,
			'blogs.type' => ContentType::BLOG,
		])->alias('blogs')->where([]);
	}
	
	/**
	 * Get content projects relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContentsProjects() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			'projects.status' => ContentStatus::ENABLED,
			'projects.type' => ContentType::PROJECT,
		])->alias('projects')->where([]);
	}
	
	/**
	 * Get content plugins relation
	 * @return \yii\db\ActiveQuery
	 */
	public function getContentsPlugins() {
		return $this->hasMany(Content::class, ['author_id' => 'id'])->onCondition([
			'plugins.status' => ContentStatus::ENABLED,
			'plugins.type' => ContentType::PLUGIN,
		])->alias('plugins')->where([]);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagQuery
	 */
	public function getTags() {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('contentTag')->orderBy([
			Tag::tableName().'.title' => SORT_ASC,
		])->where([]);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagModuleQuery
	 */
	public function getContentTag() {
		return $this->hasMany(ContentTag::class, ['author_id' => 'id']);
	}

    /**
     * Get activities
     * @return UserActivityQuery
     */
    public function getActivities() {
        return $this->hasMany(UserActivity::class, ['user_id' => 'id']);
    }
	
	/**
	 * @return array
	 */
	public function getInfo() {
		return [
			'id' => $this->id,
			'fio' => $this->getFio(),
			'username' => $this->username,
			'email' => $this->email,
			'phone' => $this->profile->phone,
			'telegram' => ($this->telegram && $this->telegram->username) ? $this->telegram->username : null,
			'image' => $this->avatar->getImageSrc(80, 80, Mode::CROP_CENTER),
		];
	}

	/**
	 * Get id
	 * @return integer
	 */
	public function getId() {
		return $this->getAttribute('id');
	}

	/**
	 * Get auth key
	 * @return mixed
	 */
	public function getAuthKey() {
		return $this->getAttribute('auth_key');
	}

	/**
	 * Check is confirmed registration
	 * @return bool Whether the user is confirmed or not.
	 */
	public function getIsConfirmed() {
		return $this->confirmed_at != null;
	}

	/**
	 * Check is blocked
	 * @return bool Whether the user is blocked or not.
	 */
	public function getIsBlocked() {
		return $this->blocked_at != null;
	}

	/**
	 * Check is deleted
	 * @return bool Whether the user is deleted or not.
	 */
	public function getIsDeleted() {
		return $this->deleted_at != null;
	}

	/**
	 * Check is admin
	 * @return bool Whether the user is an admin or not.
	 */
	public function getIsAdmin() {
		return in_array($this->username, $this->module->admins);
	}

	/**
	 * Get fullname
	 *
	 * @param bool $username
	 *
	 * @return mixed|string
	 */
	public function getFio(bool $username = true) {
		$fio = ($this->profile) ? $this->profile->fio : '';
		return (strlen($fio) || !$username) ? $fio : $this->username;
	}
	
	/**
	 * Get author name
	 * @param bool $useTelegram
	 *
	 * @return string
	 */
	public function getAuthorName($useTelegram = false) {
		$profile = $this->profile;
		
		$tmp = [];
		if (strlen($profile->first_name))
			$tmp[] = $profile->first_name;
		if (strlen($profile->last_name))
			$tmp[] = $profile->last_name;
		
		if ($useTelegram && $this->telegram && $this->telegram->username)
			$tmp[] = '(@'.$this->telegram->username.')';
		else
			$tmp[] = '('.$this->username.')';
		
		return implode(' ', $tmp);
	}
	
	/**
	 * @return bool
	 */
	public function getIsOwn() {
		return $this->id == Yii::$app->user->id;
	}

	/**
	 * Validate auth key
	 * @param string $authKey
	 *
	 * @return bool
	 */
	public function validateAuthKey($authKey) {
		return $this->getAttribute('auth_key') === $authKey;
	}

	/**
	 * Create new user account. It generate password if it is not provided by user.
	 *
	 * @return bool
	 */
	public function create($sendEmail = true) {
		if ($this->getIsNewRecord() == false) {
			throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
		}

		$this->confirmed_at = time();

		// Generate password if need
		$isGeneratePassword = ($this->password == null);

		if (is_null($this->username)) {
            $this->generateUsername();
        }

		if (is_null($this->password)) {
            $this->password = Password::generate(8);
        }

		$this->trigger(self::BEFORE_CREATE);

		if (!$this->save()) {
			return false;
		}

		if ($sendEmail) {
            $this->mailer->sendSignupMessage($this, null, true, $isGeneratePassword);
        }
		$this->trigger(self::AFTER_CREATE);

		return true;
	}

	/**
	 * This method is used to register new user account. If Module::enableConfirmation is set true, this method
	 * will generate new confirmation token and use mailer to send it to the user.
	 *
	 * @return bool
	 */
	/**
	 * @return bool
	 * @throws \yii\base\InvalidConfigException
	 */
	public function signup() {
		if ($this->getIsNewRecord() == false) {
			throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
		}

		$this->confirmed_at = $this->module->enableConfirmation ? null : time();
		$this->password = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

		$this->trigger(self::BEFORE_SIGNUP);

		if (!$this->validate() || !$this->save()) {
			return false;
		}

		if ($this->module->enableConfirmation) {
			/** @var UserToken $token */
			$token = Yii::createObject([
				'class' => UserToken::className(),
				'type' => UserToken::TYPE_CONFIRMATION
			]);
			$token->link('user', $this);
		}

		$this->mailer->sendSignupMessage($this, (isset($token) ? $token : null), true);
		$this->trigger(self::AFTER_SIGNUP);

		return true;
	}

	/**
	 * Attempts user confirmation.
	 *
	 * @param string $code Confirmation code.
	 *
	 * @return boolean
	 */
	public function attemptConfirmation($code) {
		$token = $this->finder->findTokenByParams($this->id, $code, UserToken::TYPE_CONFIRMATION);

		if ($token instanceof UserToken && !$token->isExpired) {
			$token->delete();
			if (($success = $this->confirm())) {
				Yii::$app->user->login($this, $this->module->rememberFor);
				$message = Yii::t('user', 'message_thank_you_registration_is_now_complete');
			}
			else {
				$message = Yii::t('user', 'message_something_went_wrong_and_your_account_has_not_been_confirmed');
			}
		}
		else {
			$success = false;
			$message = Yii::t('user', 'message_the_confirmation_link_is_invalid_or_expired');
		}

		Yii::$app->session->setFlash($success ? 'inline-success' : 'inline-danger', $message);

		return $success;
	}

	/**
	 * This method attempts changing user email. If user's "unconfirmed_email" field is empty is returns false, else if
	 * somebody already has email that equals user's "unconfirmed_email" it returns false, otherwise returns true and
	 * updates user's password.
	 *
	 * @param string $code
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function attemptEmailChange($code) {
		// TODO refactor method

		/** @var UserToken $token */
		$token = $this->finder->findToken([
			'user_id' => $this->id,
			'code' => $code,
		])->andWhere(['in', 'type', [
			UserToken::TYPE_CONFIRM_NEW_EMAIL,
			UserToken::TYPE_CONFIRM_OLD_EMAIL
		]])->one();

		if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
			Yii::$app->session->setFlash('danger', Yii::t('user', 'message_your_confirmation_token_is_invalid_or_expired'));
		}
		else {
			$token->delete();

			if (empty($this->unconfirmed_email)) {
				Yii::$app->session->setFlash('danger', Yii::t('user', 'message_an_error_occurred_processing_your_request'));
			}
			elseif ($this->finder->findUser(['email' => $this->unconfirmed_email])->exists() == false) {
				if ($this->module->emailChangeStrategy == Module::STRATEGY_SECURE) {
					switch ($token->type) {
						case UserToken::TYPE_CONFIRM_NEW_EMAIL:
							$this->flags |= self::NEW_EMAIL_CONFIRMED;
							Yii::$app->session->setFlash('success', Yii::t('user', 'message_awesome_almost_there_now_you_need_to_click_the_confirmation_link_sent_to_your_old_email_address'));
							break;
						case UserToken::TYPE_CONFIRM_OLD_EMAIL:
							$this->flags |= self::OLD_EMAIL_CONFIRMED;
							Yii::$app->session->setFlash('success', Yii::t('user', 'message_awesome_almost_there_now_you_need_to_click_the_confirmation_link_sent_to_your_new_email_address'));
							break;
					}
				}
				if ($this->module->emailChangeStrategy == Module::STRATEGY_DEFAULT || ($this->flags & self::NEW_EMAIL_CONFIRMED && $this->flags & self::OLD_EMAIL_CONFIRMED)) {
					$this->email = $this->unconfirmed_email;
					$this->unconfirmed_email = null;
					Yii::$app->session->setFlash('success', Yii::t('user', 'message_your_email_address_has_been_changed'));
				}
				$this->save(false);
			}
		}
	}

	/**
	 * Confirm the user by setting 'confirmed_at' field to current time.
	 *
	 * @return bool
	 */
	public function confirm() {
		return (bool)$this->updateAttributes(['confirmed_at' => time()]);
	}

	/**
	 * Reset password.
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public function resetPassword($password) {
		return (bool)$this->updateAttributes([
			'password_hash' => Password::hash($password),
		]);
	}

	/**
	 * Block the user by setting 'blocked_at' field to current time and regenerates auth_key.
	 *
	 * @return bool
	 * @throws \yii\base\Exception
	 */
	public function block() {
		return (bool)$this->updateAttributes([
			'blocked_at' => time(),
			'auth_key' => Yii::$app->security->generateRandomString(),
		]);
	}

	/**
	 * Unblock the user by setting 'blocked_at' field to null.
	 *
	 * @return bool
	 */
	public function unblock() {
		return (bool)$this->updateAttributes(['blocked_at' => null]);
	}

	/**
	 * Delete the user by setting 'deleted_at' field to null.
	 *
	 * @return bool
	 */
	public function delete($useStatus = true) {
		return (bool)$this->updateAttributes([
			'deleted_at' => time(),
			'auth_key' => Yii::$app->security->generateRandomString(),
		]);
	}

	/**
	 * Generate new username based on email address, or create new username
	 *
	 * @return string
	 */
	public function generateUsername() {

		// try to use name part of email
		$this->username = explode('@', $this->email)[0];
		if ($this->validate(['username'])) {
			return $this->username;
		}

		// generate username like "user1", "user2", etc...
		while (!$this->validate(['username'])) {
			$row = (new Query())->from(self::tableName())->select('MAX(id) as id')->one();
			$this->username = 'user' . ++$row['id'];
		}

		return $this->username;
	}

	/**
	 * @inheritdoc
	 *
	 * @param bool $insert
	 *
	 * @return bool
	 */
	public function beforeSave($insert) {
		$password = $this->password;

		if ($insert) {
			$this->setAttribute('auth_key', Yii::$app->security->generateRandomString());

			if (Yii::$app instanceof WebApplication) {
				$this->setAttribute('registration_ip', Yii::$app->request->userIP);
			}
		}

		if (!empty($password)) {
			$this->setAttribute('password_hash', Password::hash($password));
		}

		return parent::beforeSave($insert);
	}

	/**
	 * @inheritdoc
	 *
	 * @param bool $insert
	 * @param array $changedAttributes
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		if ($insert) {
			if ($this->_profile == null)
				$this->_profile = Yii::createObject(UserProfile::class);
			$this->_profile->link('user', $this);
			
			if ($this->_subscribe == null)
				$this->_subscribe = Yii::createObject(UserSubscribe::class);
			$this->_subscribe->link('user', $this);

			// Assign role user
			$auth = Yii::$app->authManager;
			$role = $auth->getRole('User');
			$auth->assign($role, $this->id);
		}
	}
}
