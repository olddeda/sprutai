<?php
namespace Longman\TelegramBot\Commands\AdminCommands;

use common\modules\base\components\Debug;
use Yii;

use yii\helpers\Url;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\InlineKeyboard;

use common\modules\telegram\commands\telegram\AdminCommand;
use common\modules\telegram\helpers\Helpers;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Type;


/**
 * Bug command
 */
class BugCommand extends AdminCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'bug';
	
	/**
	 * @var string
	 */
	protected $description = 'Добавить выделенное сообщение в задачу JIRA';
	
	/**
	 * @var string
	 */
	protected $usage = '/bug';
	
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
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function execute() {
		
		//Helpers::dump('hello');
		
		Request::sendChatAction([
			'chat_id' => $this->getChatId(),
			'action'  => ChatAction::TYPING,
		]);
		
		if ($this->getMessage() && $this->getMessage()->getReplyToMessage()) {
			$isPrivate = ($this->getChat()->getType() == 'private');
			if (!$isPrivate) {
				$reply = $this->getMessage()->getReplyToMessage();
				$isPhoto = $reply->getPhoto() ? true : false;
				
				$messageId = $this->getMessage()->getMessageId();
				$text = ($isPhoto) ? $reply->getCaption() : $reply->getText(true);
				$userName = $this->getUser()->user->getAuthorName(true);
				$url = 'https://t.me/'.$this->getChat()->getUsername().'/'.$messageId;
				
				$description = 'Ошибка: '.$text.PHP_EOL;
				$description.= 'Ссылка: '.$url.PHP_EOL;
				$description.= 'Создал: '.$userName;
				
				/** @var \common\modules\base\components\jira\Client $jira */
				$jira = Yii::$app->jira;
				$project = $jira->getProject('DEV');
				if ($project) {
					$issue = $project->createIssue('Баг');
					$issue->summary = $url;
					$issue->description = $description;
					$issue->save();
					
					if ($isPhoto) {
						$photos = $reply->getPhoto();
						if (count($photos)) {
							$photo = $photos[count($photos) - 1];
							
							$file = Request::getFile(['file_id' => $photo->getFileId()]);
							if ($file->isOk()) {
								$data = Request::downloadFile($file->getResult());
								if ($data == 'ok') {
									$filePath = $this->telegram->getDownloadPath().DIRECTORY_SEPARATOR.$file->getResult()->getFilePath();
									
									$multipart = new \GuzzleHttp\Psr7\MultipartStream([
										[
											'name' => 'file',
											'contents' => fopen($filePath, 'r'),
										],
									]);
									
									$project->client->post('issue/'.$issue->key.'/attachments', null, $multipart);
								}
							}
						}
					}
					
					$params = [
						'chat_id' => $this->getChatId(),
						'reply_to_message_id' => $this->getMessage()->getMessageId(),
						'parse_mode' => 'HTML',
						'text' => '🤦',
					];
					return Request::sendMessage($params);
				}
			}
			
		}
	}
}