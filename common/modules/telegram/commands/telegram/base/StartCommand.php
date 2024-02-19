<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Yii;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;

use common\modules\telegram\commands\telegram\SystemCommand;
use common\modules\telegram\helpers\Helpers;

use common\modules\user\models\User;
use common\modules\user\models\UserAccount;

/**
 * Start command
 */
class StartCommand extends SystemCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'start';
	
	/**
	 * @var string
	 */
	protected $description = 'Start command';
	
	/**
	 * @var string
	 */
	protected $usage = '/start';
	
	/**
	 * @var string
	 */
	protected $version = '1.1.0';
	
	/**
	 * @var bool
	 */
	protected $need_mysql = true;
	/**
	 * @var bool
	 */
	protected $private_only = true;
	
	/**
	 * Conversation Object
	 *
	 * @var \Longman\TelegramBot\Conversation
	 */
	protected $conversation;
	
	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public function execute() {
		
		/**
		 *  \common\modules\user\models\User $user
		 */
		$user = $this->getUser();
		if ($user && $user->getIsConnected()) {
			
			$text = Yii::t('telegram-base-start', 'message_welcome_user', [
				'fio' => $user->user->getAuthorName(),
				'url' => 'https://sprut.ai/client/user/settings/subscribe',
			]);
			
			return Request::sendMessage([
				'chat_id' => $this->getChatId(),
				'text' => $text,
				'parse_mode' => 'HTML',
			]);
		}
		
		/**
		 * @var \common\modules\user\models\UserAccount $account
		 */
		$account = UserAccount::find()->where('client_id = :client_id AND provider = :provider', [
			':client_id' => $this->getUserId(),
			':provider' => 'telegram',
		])->one();
		if (is_null($account)) {
			$code = UserAccount::generateCode('telegram');
			
			$account = Yii::createObject(UserAccount::class);
			$account->setAttributes([
				'client_id' => $this->getUserId(),
				'provider' => 'telegram',
				'code' => $code,
			], false);
			$account->save();
		}
		
		
		$text = Yii::t('telegram-base-start', 'message_welcome', [
			'url' => 'https://sprut.ai/client/telegram/default/link/'.$account->code,
		]);
		
		return Request::sendMessage([
			'chat_id' => $this->getChatId(),
			'text' => $text,
			'parse_mode' => 'HTML',
		]);
	}
}