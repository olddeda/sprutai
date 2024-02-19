<?php
namespace common\modules\telegram\commands\telegram;

use Yii;

use Longman\TelegramBot\Commands\Command as BaseCommand;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Entities\Update;

use common\modules\base\helpers\enum\Status;

use common\modules\telegram\Module;
use common\modules\telegram\helpers\Helpers;
use common\modules\telegram\models\TelegramUser;

abstract class Command extends BaseCommand
{
	/** @var TelegramUser */
	private $_user;
	
	/**
	 * Constructor
	 *
	 * @param \Longman\TelegramBot\Telegram $telegram
	 * @param \Longman\TelegramBot\Entities\Update $update
	 */
	public function __construct(Telegram $telegram, Update $update = null) {
		parent::__construct($telegram, $update);
		
		$this->_initUser();
	}
	
	/**
	 * Init user
	 */
	private function _initUser() {
		
		/** @var Module $module */
		$module = Yii::$app->getModule('telegram');
		
		if ($this->getUserId()) {
			$this->_user = TelegramUser::findById($this->getUserId());
			if (!$this->_user && $this->getUserId()) {
				$user = new TelegramUser();
				$user->id = $this->getUserId();
				$user->first_name = $this->getFrom()->getFirstName();
				if ($this->getFrom()->getLastName())
					$user->last_name = $this->getFrom()->getLastName();
				if ($this->getFrom()->getUsername())
					$user->username = $this->getFrom()->getUsername();
				$user->status = Status::ENABLED;
				
				if ($user->validate() && $user->save()) {
					$this->_user = $user;
				}
			}
			else if ($this->_user) {
				if ($this->getFrom()->getFirstName() != $this->_user->first_name) {
					$this->_user->first_name = $this->getFrom()->getFirstName();
				}
				if ($this->getFrom()->getLastName() && $this->getFrom()->getLastName() !== $this->_user->last_name) {
					$this->_user->last_name = $this->getFrom()->getLastName();
				}
				if ($this->getFrom()->getUsername() && $this->getFrom()->getUsername() !== $this->_user->username ) {
					$this->_user->username = $this->getFrom()->getUsername();
				}
			}
			
			$this->_user->lastvisit_at = time();
			
			$this->_user->save();
		}
	}
	
	/**
	 * Check is admin
	 * @return bool
	 */
	public function isAdmin() {
		return (in_array($this->getUserId(), $this->getModule()->adminsIds));
	}
	
	/**
	 * @return \Longman\TelegramBot\Entities\User
	 */
	public function getFrom() {
		if ($this->getUpdate()->getCallbackQuery())
			return $this->getUpdate()->getCallbackQuery()->getFrom();
		else if ($this->getMessage())
			return $this->getMessage()->getFrom();
		return null;
	}
	
	/**
	 * @return \Longman\TelegramBot\Entities\Chat
	 */
	public function getChat() {
		return ($this->getUpdate()->getCallbackQuery()) ? $this->getUpdate()->getCallbackQuery()->getMessage()->getChat() : $this->getMessage()->getChat();
	}
	
	/**
	 * @return int
	 */
	public function getChatId() {
		return $this->getChat()->getId();
	}
	
	/**
	 * User id
	 * @return int
	 */
	public function getUserId() {
		return ($this->getFrom()) ? $this->getFrom()->getId() : 0;
	}
	
	/**
	 * User
	 * @return TelegramUser
	 */
	public function getUser() {
		return $this->_user;
	}
	
	/**
	 * @return null|string
	 */
	public function getText() {
		return ($this->getCallbackQuery()) ? null : $this->getMessage()->getText(true);
	}
	
	/**
	 * @return \common\modules\telegram\Module
	 */
	public function getModule() {
		return Yii::$app->getModule('telegram');
	}
	
	/**
	 * If this is an AdminCommand
	 *
	 * @return bool
	 */
	public function isAdminCommand() {
		return ($this instanceof AdminCommand);
	}
	
	/**
	 * If this is a UserCommand
	 *
	 * @return bool
	 */
	public function isUserCommand() {
		return ($this instanceof UserCommand);
	}
	
	/**
	 * If this is a SystemCommand
	 *
	 * @return bool
	 */
	public function isSystemCommand() {
		return ($this instanceof SystemCommand);
	}
}