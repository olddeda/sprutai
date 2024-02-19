<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\File;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Entities\UserProfilePhotos;
use Longman\TelegramBot\Request;

/**
 * User "/my" command
 *
 * Simple command that returns info about the current user.
 */
class MyCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'my';
	
	/**
	 * @var string
	 */
	protected $description = 'Отображает ваш id, имя, никнейм и фотографии';
	
	/**
	 * @var string
	 */
	protected $usage = '/my';
	
	/**
	 * @var string
	 */
	protected $version = '1.0.0';
	
	/**
	 * @var bool
	 */
	protected $private_only = true;
	
	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute() {
		$message = $this->getMessage();
		
		$from       = $message->getFrom();
		$user_id    = $from->getId();
		$chat_id    = $message->getChat()->getId();
		$message_id = $message->getMessageId();
		
		$data = [
			'chat_id'             => $chat_id,
			'reply_to_message_id' => $message_id,
		];
		
		//Send chat action
		Request::sendChatAction([
			'chat_id' => $chat_id,
			'action'  => 'typing',
		]);
		
		$caption = sprintf(
			'Ваш ID: %d' . PHP_EOL .
			'Имя: %s %s' . PHP_EOL .
			'Никнейм: %s',
			$user_id,
			$from->getFirstName(),
			$from->getLastName(),
			$from->getUsername()
		);
		
		//Fetch user profile photo
		$limit    = 10;
		$offset   = null;
		$response = Request::getUserProfilePhotos(
			[
				'user_id' => $user_id,
				'limit'   => $limit,
				'offset'  => $offset,
			]
		);
		
		if ($response->isOk()) {
			/** @var UserProfilePhotos $user_profile_photos */
			$user_profile_photos = $response->getResult();
			
			if ($user_profile_photos->getTotalCount() > 0) {
				$photos = $user_profile_photos->getPhotos();
				
				/** @var PhotoSize $photo */
				$photo   = $photos[0][2];
				$file_id = $photo->getFileId();
				
				$data['photo']   = $file_id;
				$data['caption'] = $caption;
				
				$result = Request::sendPhoto($data);
				
				return $result;
			}
		}
		
		//No Photo just send text
		$data['text'] = $caption;
		
		return Request::sendMessage($data);
	}
}