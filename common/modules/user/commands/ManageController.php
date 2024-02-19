<?php
namespace common\modules\user\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use common\modules\user\Finder;
use common\modules\user\models\User;

/**
 * Manage user commands.
 *
 * @property \common\modules\user\Module $module
 */
class ManageController extends Controller
{
	/**
	 * @var Finder
	 */
	protected $finder;

	/**
	 * @param string $id
	 * @param \yii\base\Module $module
	 * @param Finder $finder
	 * @param array $config
	 */
	public function __construct($id, $module, Finder $finder, $config = []) {
		$this->finder = $finder;
		parent::__construct($id, $module, $config);
	}

	/**
	 * This command creates new user account. If password is not set, this command will generate new 8-char password.
	 * After saving user to database, this command uses mailer component to send credentials (username and password) to
	 * user via email.
	 *
	 * @param string $email Email address
	 * @param string $username Username
	 * @param null|string $password Password (if null it will be generated automatically)
	 */
	public function actionCreate($email = null, $username = null, $password = null) {
		if ($email === null)
			$email = $this->prompt('Enter E-mail:', ['required' => true]);
		if ($username === null)
			$username = $this->prompt('Enter username:', ['required' => true]);

		$user = Yii::createObject([
			'class'    => User::className(),
			'scenario' => 'create',
			'email'    => $email,
			'username' => $username,
			'password' => $password,
		]);

		if ($user->create()) {
			$this->stdout(Yii::t('user', 'User has been created')."!\n", Console::FG_GREEN);
		}
		else {
			$this->stdout(Yii::t('user', 'Please fix following errors:')."\n", Console::FG_RED);
			foreach ($user->errors as $errors) {
				foreach ($errors as $error) {
					$this->stdout(' - '.$error."\n", Console::FG_RED);
				}
			}
		}
	}

	/**
	 * Delete a user.
	 *
	 * @param string $search Email or username
	 */
	public function actionDelete($search = null) {
		if ($search === null)
			$search = $this->prompt('Enter E-mail or username:', ['required' => true]);

		if ($this->confirm(Yii::t('user', 'Are you sure? Deleted user can not be restored'))) {
			$user = $this->finder->findUserByUsernameOrEmail($search);
			if ($user === null) {
				$this->stdout(Yii::t('user', 'User is not found')."\n", Console::FG_RED);
			}
			else {
				if ($user->delete()) {
					$this->stdout(Yii::t('user', 'User has been deleted')."\n", Console::FG_GREEN);
				}
				else {
					$this->stdout(Yii::t('user', 'Error occurred while deleting user')."\n", Console::FG_RED);
				}
			}
		}
	}

	/**
	 * Update user's password to given.
	 *
	 * @param string $search Email or username
	 * @param string $password New password
	 */
	public function actionPassword($search = null, $password = null) {
		if ($search === null)
			$search = $this->prompt('Enter E-mail or username:', ['required' => true]);
		if ($password === null)
			$password = $this->prompt('Enter new password:', ['required' => true]);

		$user = $this->finder->findUserByUsernameOrEmail($search);
		if ($user === null) {
			$this->stdout(Yii::t('user', 'User is not found')."\n", Console::FG_RED);
		}
		else {
			if ($user->resetPassword($password)) {
				$this->stdout(Yii::t('user', 'Password has been changed')."\n", Console::FG_GREEN);
			}
			else {
				$this->stdout(Yii::t('user', 'Error occurred while changing password')."\n", Console::FG_RED);
			}
		}
	}

	/**
	 * Confirms a user by setting confirmed_at field to current time.
	 *
	 * @param string $search Email or username
	 */
	public function actionConfirm($search = null) {
		if ($search === null)
			$search = $this->prompt('Enter E-mail or username:', ['required' => true]);
		$user = $this->finder->findUserByUsernameOrEmail($search);
		if ($user === null) {
			$this->stdout(Yii::t('user', 'User is not found')."\n", Console::FG_RED);
		}
		else {
			if ($user->confirm()) {
				$this->stdout(Yii::t('user', 'User has been confirmed')."\n", Console::FG_GREEN);
			}
			else {
				$this->stdout(Yii::t('user', 'Error occurred while confirming user')."\n", Console::FG_RED);
			}
		}
	}
}
