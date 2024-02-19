<?php
namespace Longman\TelegramBot\Commands\AdminCommands;

use common\modules\telegram\commands\telegram\AdminCommand;
use common\modules\telegram\models\TelegramChat;
use Longman\TelegramBot\Request;

/**
 * Ban command
 */
class BanCommand extends AdminCommand  {
	
	/**
	 * @var string
	 */
	protected $name = 'ban';
	
	/**
	 * @var string
	 */
	protected $description = 'Забанить пользователя';
	
	/**
	 * @var string
	 */
	protected $usage = '/ban';
	
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

        $banCount = 0;
        $chats = TelegramChat::find()->where([
            'is_partner' => false,
        ])->all();

        /** @var TelegramChat $chat */
        if ($userId) {
            foreach ($chats as $chat) {
                $result = Request::kickChatMember([
                    'chat_id' => $chat->identifier,
                    'user_id' => $userId,
                ]);
                if ($result->isOk()) {
                    $banCount++;
                }
            }

            return Request::sendMessage([
                'chat_id' => $this->getChat()->getId(),
                'user_id' => $this->getUserId(),
                'text' => 'Забанено в '.$banCount.' чатах'
            ]);
        }

        return Request::sendMessage([
            'chat_id' => $this->getChat()->getId(),
            'user_id' => $this->getUserId(),
            'text' => 'Вы не указали user id'
        ]);
	}
}