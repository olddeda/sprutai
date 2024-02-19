<?php

namespace common\modules\user\components;

use Yii;
use yii\web\Cookie;


use common\modules\base\components\ArrayHelper;
use common\modules\base\components\Debug;

use common\modules\rbac\helpers\enum\Role;

use common\modules\user\models\User as UserModel;
use common\modules\user\models\UserLog;

/**
 * User component
 */
class User extends \yii\web\User
{
	/**
	 * @var
	 */
	private $_isSuperAdmin;
	
	/**
	 * @var
	 */
	private $_isAdmin;
	
	/**
	 * @var
	 */
	private $_isEditor;
	
	/**
	 * @var
	 */
	private $_isCompany;
	
	/**
	 * @var
	 */
	private $_isGuest;
	
	/**
	 * @inheritdoc
	 */
	public $identityClass = 'common\modules\user\models\User';

	/**
	 * @inheritdoc
	 */
	public $enableAutoLogin = true;

	/**
	 * @inheritdoc
	 */
	public $loginUrl = ["/user/signin"];

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		// Check if user is banned. if so, log user out and redirect home
		/** @var \common\modules\user\models\User $user */
		$user = $this->getIdentity();
		if ($user && ($user->getIsBlocked() || $user->getIsDeleted())) {
			$this->logout();
			Yii::$app->getResponse()->redirect(['/'])->send();
		}
	}

	/**
	 * @inheritdoc
	 */
	public function afterLogin($identity, $cookieBased, $duration) {
		parent::afterLogin($identity, $cookieBased, $duration);

		// Save user login information
		$user = $this->getIdentity();
		//$model = new UserLog();
		//$model->user_id = $user->id;
		//$model->user_agent = Yii::$app->request->getUserAgent();
		//$model->ip = Yii::$app->request->getUserIP();
		//$model->visit = time();
		//$model->save(false);
	}

	/**
	 * @inheritdoc
	 */
	public function can($permissionName, $params = [], $allowCaching = false) {
		$roles = array_keys(Yii::$app->authManager->getRolesByUser($this->id));
		if (!in_array($permissionName, $roles) && strpos(Yii::$app->id, $permissionName) === false) {
            if (Yii::$app->id !== 'api') {
                $permissionName = Yii::$app->id.'.'.$permissionName;
            }
        }
		return parent::can($permissionName, $params, $allowCaching);
	}

	/**
	 * Check if user is superadmin
	 *
	 * @return bool
	 */
	public function getIsSuperAdmin() {
		if (is_null($this->_isSuperAdmin)) {
			$this->_isSuperAdmin = false;
			if (!$this->isGuest)
				$this->_isSuperAdmin = in_array(Yii::$app->user->identity->username, Yii::$app->getModule('user')->admins);
		}
		return $this->_isSuperAdmin;
	}
	
	/**
	 * Check if user is admin
	 *
	 * @return bool
	 */
	public function getIsAdmin() {
		if (is_null($this->_isAdmin)) {
			$this->_isAdmin = false;
			if ($this->getIsSuperAdmin())
				$this->_isAdmin = true;
			$roles = Yii::$app->authManager->getRolesByUser($this->getId());
			if ($roles)
				$this->_isAdmin = in_array('Admin', array_keys($roles));
		}
		return $this->_isAdmin;
	}
	
	/**
	 * Check if user is editor
	 *
	 * @return bool
	 */
	public function getIsEditor() {
		if (is_null($this->_isEditor)) {
			$this->_isEditor = false;
			if ($this->getIsSuperAdmin())
				$this->_isEditor = true;
			$roles = Yii::$app->authManager->getRolesByUser($this->getId());
			if ($roles)
				$this->_isEditor = in_array('Editor', array_keys($roles));
		}
		return $this->_isEditor;
	}
	
	/**
	 * Check if user is company
	 *
	 * @return bool
	 */
	public function getIsCompany() {
		if (is_null($this->_isCompany)) {
			$this->_isCompany = false;
			if ($this->getIsSuperAdmin())
				$this->_isCompany = true;
			$roles = Yii::$app->authManager->getRolesByUser($this->getId());
			if ($roles)
				$this->_isCompany = in_array('Company', array_keys($roles));
		}
		return $this->_isCompany;
	}

	/**
	 * Check if user is logged in
	 *
	 * @return bool
	 */
	public function getIsLoggedIn() {
		return !$this->getIsGuest();
	}
	
	/**
	 * Get role
	 * @return mixed
	 */
	public function getRole() {
		return current($this->getRoles());
	}
	
	/**
	 * Get roles
	 * @return array|\yii\rbac\Role[]
	 */
	public function getRoles() {
		$roles = Yii::$app->authManager->getRolesByUser($this->id);
		if (!count($roles))
			$roles = [Yii::$app->authManager->getRole('Guest')];
		return $roles;
	}

    /**
     * Get assigments
     * @return array|\yii\rbac\Assignment[]
     */
    public function getAssigments() {
        return Yii::$app->authManager->getAssignments($this->id);
    }
	
	/**
	 * Check role
	 * @param $needRole
	 *
	 * @return bool
	 */
	public function hasRole($needRole) {
		$needRoles = [];
		if (is_array($needRole)) {
			foreach ($needRole as $nR)
				$needRoles[] = Role::getLabel($nR);
		}
		else
			$needRoles[] = Role::getLabel($needRole);
		
		$roles = [];
		foreach ($this->getRoles() as $role)
			$roles[] = $role->name;
		
		$exists = false;
		foreach ($roles as $r) {
			if ($exists)
				break;
			
			if ($r == Role::getLabel(Role::SUPERADMIN) || $r == Role::getLabel(Role::ADMIN)) {
				$exists = true;
				break;
			}
			
			foreach ($needRoles as $nR) {
				if ($nR == $r) {
					$exists = true;
					break;
				}
			}
		}
		
		return $exists;
	}
	
	/**
	 * Get roles for user
	 * @param UserModel $user
	 *
	 * @return \yii\rbac\Role[]
	 */
	static public function getRolesFor(UserModel $user) {
		return Yii::$app->authManager->getRolesByUser($user->getId());
	}

	static public function getAssigmentsFor(UserModel $user) {
        return Yii::$app->authManager->getAssignments($user->getId());
    }
		
		/**
	 * Get admins
	 * @return array|\common\modules\user\models\User[]
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getAdmins() {
		$ids = array_unique(ArrayHelper::merge(
			Yii::$app->authManager->getUserIdsByRole(Role::getItem(Role::SUPER_ADMIN)),
			Yii::$app->authManager->getUserIdsByRole(Role::getItem(Role::ADMIN))
		));
		return UserModel::find()->andWhere(['in', 'id', $ids])->all();
	}
	
	/**
	 * Get theme style
	 * @param string $name
	 *
	 * @return string
	 */
	public function themeStyle($name = 'themeStyle') {
		if (!isset($_COOKIE[$name]))
			return $this->setThemeStyle();
		return $_COOKIE[$name];
	}
	
	/**
	 * @param UserModel $user
	 *
	 * @param boolean $allowOwn
	 *
	 * @return bool
	 */
	public function canAccess(UserModel $user, $allowOwn = false) {
		$ownLevel = self::getLevelFor($this->identity);
		$himLevel = self::getLevelFor($user);
		
		$adminId = Yii::$app->session->get('admin_id');
		
		if ($user->id == $this->id)
			return $allowOwn;
		
		if ($adminId && $adminId == $user->id)
			return false;
		
		if ($this->getIsSuperAdmin())
			return true;
		
		return $ownLevel > $himLevel;
	}
	
	/**
	 * Get level
	 * @return int
	 */
	public function getLevel() {
		return self::getLevelFor($this->identity);
	}
	
	/**
	 * Get level for user
	 * @param UserModel $user
	 *
	 * @return int
	 */
	static public function getLevelFor(UserModel $user) {
		$roles = array_keys(self::getRolesFor($user));
		
		if (in_array('SuperAdmin', $roles))
			return 100;
		else if (in_array('Admin', $roles))
			return 99;
		else if (in_array('Editor', $roles))
			return 2;
		else if (in_array('User', $roles))
			return 1;
		
		return 0;
	}
	
	/**
	 * Set scheme style
	 * @param string $val
	 * @param string $name
	 *
	 * @return string
	 */
	public function setThemeStyle($name = 'themeStyle', $val = 'light') {
		setcookie($name, $val, time() + 60 * 60 * 24 * 365, '/');
		return $val;
	}
}