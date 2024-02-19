<?php
namespace common\modules\user;

use common\modules\user\models\User;
use yii\base\BaseObject;

use common\modules\base\components\ActiveQuery;

use common\modules\base\components\Debug;

use common\modules\user\models\query\UserAccountQuery;

/**
 * Finder provides some useful methods for finding active record models.
 */
class Finder extends BaseObject
{
	/** @var ActiveQuery */
	protected $userQuery;

	/** @var ActiveQuery */
	protected $profileQuery;
	
	/** @var ActiveQuery */
	protected $addressQuery;
	
	/** @var ActiveQuery */
	protected $subscribeQuery;

	/** @var ActiveQuery */
	protected $tokenQuery;

	/** @var UserAccountQuery */
	protected $accountQuery;

	/**
	 * @return ActiveQuery
	 */
	public function getUserQuery() {
		return $this->userQuery;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getProfileQuery() {
		return $this->profileQuery;
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getAddressQuery() {
		return $this->addressQuery;
	}
	
	/**
	 * @return ActiveQuery
	 */
	public function getSubscribeQuery() {
		return $this->subscribeQuery;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getTokenQuery() {
		return $this->tokenQuery;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getAccountQuery() {
		return $this->accountQuery;
	}

	/** @param ActiveQuery $userQuery */
	public function setUserQuery(ActiveQuery $userQuery) {
		$this->userQuery = $userQuery;
	}

	/** @param ActiveQuery $profileQuery */
	public function setProfileQuery(ActiveQuery $profileQuery) {
		$this->profileQuery = $profileQuery;
	}
	
	/** @param ActiveQuery $addressQuery */
	public function setAddressQuery(ActiveQuery $addressQuery) {
		$this->addressQuery = $addressQuery;
	}
	
	/** @param ActiveQuery $subscribeQuery */
	public function setSubscribeQuery(ActiveQuery $subscribeQuery) {
		$this->subscribeQuery = $subscribeQuery;
	}

	/** @param ActiveQuery $tokenQuery */
	public function setTokenQuery(ActiveQuery $tokenQuery) {
		$this->tokenQuery = $tokenQuery;
	}

	/** @param ActiveQuery $accountQuery */
	public function setAccountQuery(ActiveQuery $accountQuery) {
		$this->accountQuery = $accountQuery;
	}

	/**
	 * Finds a user by the given id.
	 *
	 * @param int $id User id to be used on search.
	 *
	 * @return models\User
	 */
	public function findUserById($id) {
		return $this->findUser([User::tableName().'.id' => $id])->votes()->one();
	}

	/**
	 * Finds a user by the given username.
	 *
	 * @param string $username Username to be used on search.
	 *
	 * @return models\User
	 */
	public function findUserByUsername($username) {
		return $this->findUser(['username' => $username])->one();
	}

	/**
	 * Finds a user by the given email.
	 *
	 * @param string $email Email to be used on search.
	 *
	 * @return models\User
	 */
	public function findUserByEmail($email) {
		return $this->findUser(['email' => $email])->one();
	}

	/**
	 * Finds a user by the given username or email.
	 *
	 * @param string $usernameOrEmail Username or email to be used on search.
	 *
	 * @return models\User
	 */
	public function findUserByUsernameOrEmail($usernameOrEmail) {
		if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
			return $this->findUserByEmail($usernameOrEmail);
		}

		return $this->findUserByUsername($usernameOrEmail);
	}

	/**
	 * Finds a user by the given condition.
	 *
	 * @param mixed $condition Condition to be used on search.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function findUser($condition) {
		return $this->userQuery->andWhere($condition);
	}

	/**
	 * @return UserAccountQuery
	 */
	public function findAccount() {
		return $this->accountQuery;
	}

	/**
	 * Finds an account by id.
	 *
	 * @param int $id
	 *
	 * @return models\Account|null
	 */
	public function findAccountById($id) {
		return $this->accountQuery->where(['id' => $id])->one();
	}

	/**
	 * Finds a token by user id and code.
	 *
	 * @param mixed $condition
	 *
	 * @return ActiveQuery
	 */
	public function findToken($condition) {
		return $this->tokenQuery->where($condition);
	}

	/**
	 * Finds a token by params.
	 *
	 * @param integer $userId
	 * @param string $code
	 * @param integer $type
	 *
	 * @return Token
	 */
	public function findTokenByParams($userId, $code, $type) {
		return $this->findToken([
			'user_id' => $userId,
			'code' => $code,
			'type' => $type,
		])->one();
	}

	/**
	 * Finds a profile by user id.
	 *
	 * @param int $id
	 *
	 * @return null|models\UserProfile
	 */
	public function findProfileById($id) {
		return $this->findProfile(['user_id' => $id])->one();
	}

	/**
	 * Finds a profile.
	 *
	 * @param mixed $condition
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function findProfile($condition) {
		return $this->profileQuery->where($condition);
	}
}
