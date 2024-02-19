<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use common\modules\media\helpers\enum\Mode;
use Yii;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;

use common\modules\telegram\commands\telegram\UserCommand;
use common\modules\telegram\helpers\Helpers;

use common\modules\content\models\Blog;
use common\modules\content\helpers\enum\Status;

/**
 * Blog command
 */
class BlogCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'blog';
	
	/**
	 * @var string
	 */
	protected $description = 'Добавление записи в блог';
	
	/**
	 * @var string
	 */
	protected $usage = '/blog';
	
	/**
	 * @var string
	 */
	protected $version = '1.0.0';
	
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
	 * @var Longman\TelegramBot\Entities\CallbackQuery
	 */
	protected $callback_query;
	
	/**
	 * @var Longman\TelegramBot\Entities\Message
	 */
	protected $message;
	
	/**
	 * @var array
	 */
	protected $notes;
	
	/**
	 * @var array
	 */
	protected $data;
	
	/**
	 * @var string
	 */
	protected $text;
	
	/**
	 * @var integer
	 */
	protected $chat_id;
	
	/**
	 * @var integer
	 */
	protected $user_id;
	
	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute() {
		$this->callback_query = $this->getUpdate()->getCallbackQuery();
		$this->message = ($this->callback_query) ? $this->callback_query->getMessage() : $this->getMessage();
		
		$this->text = ($this->callback_query) ? '' : $this->message->getText(true);
		$this->chat_id = $this->getChatId();
		$this->user_id = $this->getUserId();
		
		if ($this->callback_query && $this->callback_query->getData()) {
			$data_callback ['callback_query_id'] = $this->callback_query->getId();
			Request::answerCallbackQuery($data_callback);
		}
		
		// Preparing Response
		$this->data = [
			'chat_id' => $this->chat_id,
			'reply_markup' => Keyboard::remove(['selective' => true]),
			'parse_mode' => 'HTML',
		];
		
		$user = $this->getUser();
		if (!$user || !$user->getIsConnected()) {
			$this->data['text'] = Yii::t('telegram-base-blog', 'message_not_register');
			return Request::sendMessage($this->data);
		}
		
		// Conversation start
		$this->conversation = new Conversation($this->user_id, $this->chat_id, $this->getName());
		$this->notes = &$this->conversation->notes;
		
		if ($this->callback_query) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->getCallbackQuery()->getData(), $match)) {
				if (count($match) == 3) {
					if ($match[1] == 'change') {
						$field = $match[2];
						
						$this->notes['fieldChange']['field'] = $field;
						$this->notes['fieldChange']['message_ids'][] = $this->message->getMessageId();
						
						$this->conversation->update();
						$this->callback_query = null;
					}
					else if ($match[1] == 'action') {
						if ($match[2] == 'change_cancel') {
							$this->clearFieldChange();
							$this->callback_query = null;
						}
						else if ($match[2] == 'cancel') {
							$this->clearFieldChange();
							$this->conversation->stop();
							
							return Request::deleteMessage([
								'chat_id' => $this->chat_id,
								'message_id' => $this->message->getMessageId(),
							]);
						}
					}
					
				}
			}
		}
		
		
		if (!count($this->notes)) {
			$this->notes = [
				'state' => 0,
				'complete' => false,
				'fields' => [
					'title' => null,
					'descr' => null,
					'text' => null,
					'image' => null,
				],
			];
		}
		
		foreach ($this->notes['fields'] as $key => $val) {
			if (is_null($val)) {
				$result = call_user_func([$this, 'askField'.ucfirst($key)]);
				if ($result) {
					return $result;
				}
			}
		}
		
		if (isset($this->notes['fieldChange'])) {
			$field = $this->notes['fieldChange']['field'];
			$result = call_user_func([$this, 'askField'.ucfirst($field)], true);
			
			if ($result) {
				$this->notes['fieldChange']['message_ids'][] = $result->result->getMessageId();
				$this->conversation->update();
				return $result;
			}
		}
		
		foreach ($this->notes['fields'] as $key => $val) {
			$this->notes['fields'][$key] = (is_array($val)) ? $val : strip_tags($val);
		}
		
		$result = $this->confirm();
		if ($result)
			return $result;
		
		return Request::emptyResponse();
	}
	
	/**
	 * Ask user about title
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function askFieldTitle($change = false) {
		if (empty($this->text)) {
			$this->notes['state'] = 0;
			$this->conversation->update();
			
			if ($change) {
				$this->data['text'] = Yii::t('telegram-base-blog', 'tip_change_title');
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_change_cancel'), 'callback_data' => 'action:change_cancel']],
					],
				];
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-base-blog', 'tip_title'));
				
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_cancel'), 'callback_data' => 'action:cancel']],
					],
				];
			}
			
			return Request::sendMessage($this->data);
		}
		else {
			$this->saveField('title', $this->text);
		}
	}
	
	/**
	 * Ask user about descr
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function askFieldDescr($change = false) {
		if (empty($this->text)) {
			$this->notes['state'] = 1;
			$this->conversation->update();
			
			if ($change) {
				$this->data['text'] = Yii::t('telegram-base-blog', 'tip_change_descr');
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_change_cancel'), 'callback_data' => 'action:change_cancel']],
					],
				];
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-base-blog', 'tip_descr'));
				
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_cancel'), 'callback_data' => 'action:cancel']],
					],
				];
			}
			
			return Request::sendMessage($this->data);
		}
		else {
			$this->saveField('descr', $this->text);
		}
	}
	
	/**
	 * Ask user about text
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function askFieldText($change = false) {
		if (empty($this->text)) {
			$this->notes['state'] = 2;
			$this->conversation->update();
			
			if ($change) {
				$this->data['text'] = Yii::t('telegram-base-blog', 'tip_change_text');
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_change_cancel'), 'callback_data' => 'action:change_cancel']],
					],
				];
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-base-blog', 'tip_text'));
				
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_cancel'), 'callback_data' => 'action:cancel']],
					],
				];
			}
			
			return Request::sendMessage($this->data);
		}
		else {
			$this->saveField('text', $this->text);
		}
	}
	
	/**
	 * Ask user about image
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function askFieldImage($change = false) {
		if ($this->callback_query) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->getCallbackQuery()->getData(), $match)) {
				if (count($match) == 3 && $match[2] == 'skip') {
					$this->callback_query = null;
					$this->saveField('image', false);
				}
			}
		}
		else if ($this->message->getType() == 'photo' && !$this->message->getCaption()) {
			$doc = $this->message->getPhoto();
			$doc = end($doc);
			
			$file = Request::getFile(['file_id' => $doc->getFileId()]);
			if ($file->isOk() && Request::downloadFile($file->getResult())) {
				$filePath = $this->telegram->getDownloadPath().'/'.$file->getResult()->getFilePath();
				$this->saveField('image', [
					'id' => $doc->getFileId(),
					'path' => $filePath,
				]);
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-base-blog', 'tip_image_failed'));
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_skip'), 'callback_data' => 'action:skip']],
						[['text' => Yii::t('telegram-base-blog', 'button_cancel'), 'callback_data' => 'action:cancel']],
					],
				];
				
				return Request::sendMessage($this->data);
			}
		}
		else {
			$this->notes['state'] = 3;
			$this->conversation->update();
			
			if ($change) {
				$this->data['text'] = Yii::t('telegram-base-blog', 'tip_change_image');
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_change_cancel'), 'callback_data' => 'action:change_cancel']],
					],
				];
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-base-blog', 'tip_image'));
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-base-blog', 'button_skip'), 'callback_data' => 'action:skip']],
					],
				];
			}
			
			return Request::sendMessage($this->data);
			
		}
	}
	
	/**
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function confirm() {
		if ($this->callback_query) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->getCallbackQuery()->getData(), $match)) {
				if (count($match) == 3) {
					if ($match[1] == 'action') {
						return $this->save($match[2]);
					}
				}
			}
		}
		else {
			$text = '<b>'.$this->notes['fields']['title'].'</b>'.PHP_EOL;
			$text.= '<i>'.$this->notes['fields']['descr'].'</i>'.PHP_EOL;
			$text.= PHP_EOL;
			$text.= $this->notes['fields']['text'];
			
			$isPhoto = (is_array($this->notes['fields']['image']));
			if ($isPhoto) {
				$this->data['caption'] = $text;
				$this->data['photo'] = Request::encodeFile($this->notes['fields']['image']['path']);
			}
			else {
				$this->data['text'] = $text;
			}
			
			$this->data['reply_markup'] = [
				'inline_keyboard' => [
					[['text' => Yii::t('telegram-base-blog', 'button_change_title'), 'callback_data' => 'change:title']],
					[['text' => Yii::t('telegram-base-blog', 'button_change_descr'), 'callback_data' => 'change:descr']],
					[['text' => Yii::t('telegram-base-blog', 'button_change_text'), 'callback_data' => 'change:text']],
					[['text' => Yii::t('telegram-base-blog', 'button_change_image'), 'callback_data' => 'change:image']],
					[
						['text' => Yii::t('telegram-base-blog', 'button_publish'), 'callback_data' => 'action:publish'],
						['text' => Yii::t('telegram-base-blog', 'button_draft'), 'callback_data' => 'action:draft'],
					], [
						['text' => Yii::t('telegram-base-blog', 'button_cancel'), 'callback_data' => 'action:cancel']
					],
				],
			];
			
			return ($isPhoto) ? Request::sendPhoto($this->data) : Request::sendMessage($this->data);
		}
	}
	
	/**
	 * Save to blog
	 * @param $type
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	protected function save($type) {
		$fields = $this->notes['fields'];
		
		$isPhoto = (is_array($this->notes['fields']['image']));
		
		$text = '<div class="row"><div class="col-md-12"><p>'.$fields['text'].'</p></div></div>';
		
		$userId = $this->getUser()->user->id;
		
		$blog = new Blog();
		$blog->title = $fields['title'];
		$blog->descr = $fields['descr'];
		$blog->text = $text;
		$blog->status = ($type == 'publish') ? Status::ENABLED : Status::DRAFT;
		$blog->date_at = time();
		$blog->author_id = $userId;
		$blog->created_by = $userId;
		$blog->updated_by = $userId;
		
		$blog->detachBehavior('modifiedby');
		
		if ($blog->save()) {
			if ($isPhoto) {
				$blog->image->saveFile($this->notes['fields']['image']['path']);
				
				$imageInfo = $blog->image->getImageInfo(true);
				
				$imageUrlOriginal = $imageInfo['http'].'3000x_'.$imageInfo['file'];
				$imageUrlThumb = $imageInfo['http'].'1600x_'.$imageInfo['file'];
				$blog->text = '<div class="row"><div class="col-md-12"><a href="'.$imageUrlOriginal.'" title="" class="is-lightbox"><img src="'.$imageUrlThumb.'" id="img-1"></a></div></div>'.PHP_EOL.$blog->text;
				
				$blog->save();
			}
			
			Request::deleteMessage([
				'chat_id' => $this->chat_id,
				'message_id' => $this->message->getMessageId(),
			]);
			
			$this->data['text'] = Yii::t('telegram-base-blog', 'message_'.$type.'_success');
			Request::sendMessage($this->data);
			
			$text = '<b>'.$this->notes['fields']['title'].'</b>'.PHP_EOL;
			$text.= '<i>'.$this->notes['fields']['descr'].'</i>'.PHP_EOL;
			$text.= PHP_EOL;
			$text.= $this->notes['fields']['text'];
			
			if ($isPhoto) {
				$this->data['caption'] = $text;
				$this->data['photo'] = Request::encodeFile($this->notes['fields']['image']['path']);
			}
			else {
				$this->data['text'] = $text;
			}
			
			$this->data['reply_markup'] = [
				'inline_keyboard' => [
					[['text' => Yii::t('telegram-base-blog', 'button_view_portal'), 'url' => 'https://sprut.ai/blog/'.$blog->id]],
					[['text' => Yii::t('telegram-base-blog', 'button_edit_portal'), 'url' => 'https://sprut.ai/content/blog/update/'.$blog->id]],
				],
			];
			
			$this->conversation->stop();
			
			return ($isPhoto) ? Request::sendPhoto($this->data) : Request::sendMessage($this->data);
		}
		else {
			$this->data['text'] = Yii::t('telegram-base-blog', 'message_'.$type.'_failed');
			
			return Request::sendMessage($this->data);
		}
	}
	
	/**
	 * Save field to notes
	 *
	 * @param string $field
	 * @param string $val
	 */
	protected function saveField($field, $val) {
		if (isset($this->notes['fieldChange'])) {
			$this->clearFieldChange();
		}
		
		$this->notes['fields'][$field] = (is_array($val)) ? $val : strip_tags($val);
		$this->text = null;
		$this->conversation->update();
	}
	
	/**
	 * Clear fields and creates messages
	 */
	protected function clearFieldChange() {
		if (!isset($this->notes['fieldChange']))
			return;
		
		$messageIds = $this->notes['fieldChange']['message_ids'];
		unset($this->notes['fieldChange']);
		
		$this->conversation->update();
		
		foreach ($messageIds as $messageId) {
			Request::deleteMessage([
				'chat_id' => $this->chat_id,
				'message_id' => $messageId,
			]);
		}
	}
	
	/**
	 * Get prefix for field message
	 *
	 * @param $text
	 *
	 * @return mixed
	 */
	protected function getFieldTextPrefix($text) {
		$state = 0;
		foreach ($this->notes['fields'] as $field) {
			if (!empty($field))
				$state++;
		}
		return $this->stepTextPrefix($text, $state, count($this->notes['fields']));
	}
	
	/**
	 * @param string $text
	 * @param int $currentStep
	 * @param int $totalStep
	 *
	 * @return string
	 */
	static public function stepTextPrefix(string $text, int $currentStep, int $totalStep) {
		$str = '';
		for ($i = 0; $i < $totalStep; $i++) {
			$str .= ($i == $currentStep) ? '๏' : '◌';
			
		}
		$str .= ' '.$text;
		return $str;
	}
}