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
 * Meta command
 */
class MetaCommand extends UserCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'meta';
	
	/**
	 * @var string
	 */
	protected $description = 'Как правильно задавать вопрос';
	
	/**
	 * @var string
	 */
	protected $usage = '/meta';
	
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

	    if (!in_array($this->getChatId(), [
            -1001419924663, // Вопросы
            -1001235314744, // SprutHub Test 2
            -1001249733375, // Zigbee
            -1001440627681, // Zwave
        ])) {
	        return;
        }
		
		Request::sendChatAction([
			'chat_id' => $this->getChatId(),
			'action'  => ChatAction::TYPING,
		]);
		
		$params = [
			'chat_id' => $this->getChatId(),
			'reply_to_message_id' => $this->getMessage()->getMessageId(),
			'parse_mode' => 'HTML',
			'text' => 'Что бы получить исчерпывающий и точный ответ на свой вопрос, необходимо правильно полно и корректно донести его до разработчика.'.PHP_EOL.'https://wiki.sprut.ai/ru/spruthub/make-correct-questions',
		];
		
		return Request::sendMessage($params);
	}
}