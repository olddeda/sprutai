<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Yii;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Update;

use Longman\TelegramBot\Commands\UserCommands\NewCommand;
use Longman\TelegramBot\Commands\UserCommands\EventCommand;


/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;
    
	/**
	 * Execution if MySQL is required but not available
	 *
	 * @return ServerResponse
	 */
    public function executeNoDb() {
        return Request::emptyResponse();
    }

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute()
    {
		$text = trim($this->getMessage()->getText(true));
		$update = json_decode($this->update->toJson(), true);
	
		// If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );
		
        // Fetch conversation command if it exists and execute it
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
       	}
	
		return $this->getTelegram()->executeCommand('new');
    }
}
