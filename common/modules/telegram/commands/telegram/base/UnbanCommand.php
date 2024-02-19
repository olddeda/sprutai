<?php
namespace Longman\TelegramBot\Commands\AdminCommands;

use common\modules\telegram\commands\telegram\AdminCommand;
use common\modules\telegram\models\TelegramChat;
use Longman\TelegramBot\Request;

/**
 * Unban command
 */
class UnbanCommand extends AdminCommand  {
	
	/**
	 * @var string
	 */
	protected $name = 'unban';
	
	/**
	 * @var string
	 */
	protected $description = 'Разбанить пользователя';
	
	/**
	 * @var string
	 */
	protected $usage = '/unban';
	
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

        $userId = null;

        $replyMessage = $this->getMessage()->getReplyToMessage();
        if ($replyMessage) {
            $userId = $replyMessage->getForwardFrom()->getId();
        }
        else {
            $userId = (int)$this->getText();
        }

        $unbanCount = 0;
        $chats = TelegramChat::find()->where([
            'is_partner' => false,
        ])->all();

        /** @var TelegramChat $chat */
        if ($userId) {
            foreach ($chats as $chat) {
                $result = Request::unbanChatMember([
                    'chat_id' => $chat->identifier,
                    'user_id' => $userId,
                ]);
                if ($result->isOk()) {
                    $unbanCount++;
                }
            }

            return Request::sendMessage([
                'chat_id' => $this->getChat()->getId(),
                'user_id' => $this->getUserId(),
                'text' => 'Разбанено в '.$unbanCount.' чатах'
            ]);
        }

        return Request::sendMessage([
            'chat_id' => $this->getChat()->getId(),
            'user_id' => $this->getUserId(),
            'text' => 'Вы не указали user id'
        ]);
	}
}