<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class HelpCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'help';
	
	/**
	 * @var string
	 */
	protected $description = 'Помощь';
	
	/**
	 * @var string
	 */
	protected $usage = '/help or /help <command>';
	
	/**
	 * @var string
	 */
	protected $version = '1.0.0';
	
	/**
	 * @var bool
	 */
	protected $private_only = false;
	
	/**
	 * @inheritdoc
	 */
	public function execute() {
		$message = $this->getMessage();
		$chatId = $message->getChat()->getId();
		$commandStr = trim($message->getText(true));
		
		// Admin commands shouldn't be shown in group chats
		$safeToShow = $message->getChat()->isPrivateChat();
		
		$data = [
			'chat_id' => $chatId,
			'parse_mode' => 'markdown',
		];
		
		list($allCommands, $userCommands, $adminCommands) = $this->getUserAdminCommands();
		
		// If no command parameter is passed, show the list.
		if ($commandStr === '') {
			$data['text'] = '*Список команд*:'.PHP_EOL;
			foreach ($userCommands as $user_command) {
				$data['text'] .= '/'.$user_command->getName().' - '.$user_command->getDescription().PHP_EOL;
			}
			
			if ($safeToShow && count($adminCommands) > 0) {
				$data['text'] .= PHP_EOL.'*Список команд админа*:'.PHP_EOL;
				foreach ($adminCommands as $adminCommand) {
					$data['text'] .= '/' .$adminCommand->getName().' - '.$adminCommand->getDescription().PHP_EOL;
				}
			}
			
			$data['text'] .= PHP_EOL . 'Для справки по команде введите: /help <command>';
			
			return Request::sendMessage($data);
		}
		
		$commandStr = str_replace('/', '', $commandStr);
		if (isset($allCommands[$commandStr]) && ($safeToShow || !$allCommands[$commandStr]->isAdminCommand())) {
			$command = $allCommands[$commandStr];
			$data['text'] = sprintf(
				'Команда: %s (v%s)'.PHP_EOL .
				'Описание: %s'.PHP_EOL.
				'Использование: %s',
				$command->getName(),
				$command->getVersion(),
				$command->getDescription(),
				$command->getUsage()
			);
			
			return Request::sendMessage($data);
		}
		
		$data['text'] = 'Помощь не доступна: Команда /'.$commandStr.' не найдена';
		
		return Request::sendMessage($data);
	}
	
	/**
	 * Get all available User and Admin commands to display in the help list.
	 *
	 * @return Command[][]
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function getUserAdminCommands() {
		
		/** @var Command[] $commands */
		$commands = array_filter($this->telegram->getCommandsList(), function ($command) {
			/** @var Command $command */
			return !$command->isSystemCommand() && $command->showInHelp() && $command->isEnabled();
		});
		
		$userCommands = array_filter($commands, function ($command) {
			/** @var Command $command */
			return $command->isUserCommand();
		});
		
		$adminCommands = array_filter($commands, function ($command) {
			/** @var Command $command */
			return $command->isAdminCommand();
		});
		
		ksort($commands);
		ksort($userCommands);
		ksort($adminCommands);
		
		return [$commands, $userCommands, $adminCommands];
	}
}