<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use common\modules\base\components\Debug;
use Yii;

use yii\helpers\Json;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\InlineKeyboard;

use common\modules\telegram\commands\telegram\UserCommand;
use common\modules\telegram\helpers\Helpers;

use common\modules\media\components\Image;

use common\modules\user\models\User;
use common\modules\user\models\UserAccount;

use common\modules\content\models\ContentAuthorStat;

/**
 * Myid command
 */
class MyidCommand extends UserCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'myid';
	
	/**
	 * @var string
	 */
	protected $description = 'Отобразить мой id';
	
	/**
	 * @var string
	 */
	protected $usage = '/myid';
	
	/**
	 * @var string
	 */
	protected $version = '1.0.0';
	
	/**
	 * @var bool
	 */
	protected $need_mysql = true;
	
	/**
	 * @var bool
	 */
	protected $private_only = false;
	
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
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute() {
		
		Request::sendChatAction([
			'chat_id' => $this->getChatId(),
			'action'  => ChatAction::TYPING,
		]);
		
		$params = [
			'chat_id' => $this->getChatId(),
			'reply_to_message_id' => $this->getMessage()->getMessageId(),
			'parse_mode' => 'HTML',
			'text' => Yii::t('telegram-base-myid', 'message', ['id' => $this->getChatId()]),
		];
		
		return Request::sendMessage($params);
	}
}