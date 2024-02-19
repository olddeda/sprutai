<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;
use yii\validators\UrlValidator;

use Longman\TelegramBot\Conversation;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;

use Longman\TelegramBot\Request;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;

use common\modules\base\helpers\enum\Status;

use common\modules\event\models\Event;

use common\modules\telegram\commands\telegram\UserCommand;
use common\modules\telegram\helpers\Helpers;
use common\modules\telegram\models\TelegramUser;
use common\modules\telegram\models\TelegramRegion;
use common\modules\telegram\models\TelegramProject;
use common\modules\telegram\models\TelegramCategory;
use common\modules\telegram\helpers\Answers;
use common\modules\telegram\helpers\enum\Role;

/**
 * User "/signup" command
 */
class SignupCommand extends UserCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'signup';
	
	/**
	 * @var string
	 */
	protected $description = 'Signup new users';
	
	/**
	 * @var string
	 */
	protected $usage = '/signup';
	
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
	protected $private_only = true;
	
	/**
	 * Conversation Object
	 *
	 * @var \Longman\TelegramBot\Conversation
	 */
	protected $conversation;
	
	/**
	 * @var \common\modules\telegram\Module
	 */
	protected $module;
	
	protected $data;
	protected $text;
	protected $chat_id;
	protected $user_id;
	protected $notes;
	protected $message;
	protected $callback_query;
	protected $from;
	
	protected $projects_count;
	
	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 */
	public function execute() {
		$user = $this->getUser();
		$this->projects_count = $user->getProjects()->active()->count();
		if (!$user->checkNeedComplete(Role::EXECUTOR) && $this->projects_count) {
			return $this->getTelegram()->executeCommand('profile');
		}
		
		$this->message = $this->getMessage();
		$this->callback_query = $this->getUpdate()->getCallbackQuery();
		$this->from = ($this->callback_query) ? $this->callback_query->getFrom() : $this->message->getFrom();
		
		$this->text = ($this->callback_query) ? '' : $this->message->getText(true);
		if (empty($this->text) || in_array($this->text, [
			Yii::t('telegram-executor-profile', 'button_projects'),
			Yii::t('telegram-executor-profile', 'button_contacts'),
		]))
			$this->text = '';
		$event = Event::current();
		if ($event && $this->text == $event->title)
			$this->text = '';
		
		$this->chat_id = $this->getChatId();
		$this->user_id = $this->getUserId();
		
		if ($this->callback_query && $this->callback_query->getData()) {
			$data_callback ['callback_query_id'] = $this->callback_query->getId();
			Request::answerCallbackQuery($data_callback);
		}
		
		// Preparing Response
		$this->data = [
			'chat_id' => $this->chat_id,
			//'reply_markup' => Keyboard::remove(['selective' => true]),
			'parse_mode' => 'HTML',
		];
		
		// Conversation start
		$this->conversation = new Conversation($this->user_id, $this->chat_id, $this->getName());
		$this->notes = &$this->conversation->notes;
		
		$user = $this->getUser();
		if (!count($this->notes)) {
			
			$this->notes = [
				'state' => 0,
				'category' => 0,
				'region' => null,
				'complete' => false,
			];
			
			if ($this->projects_count) {
				
				$fields = [];
				if (empty($user->phone))
					$fields['phone'] = '';
				if (empty($user->region_id))
					$fields['region'] = '';
				if (empty($user->email))
					$fields['email'] = '';
				if (is_null($user->is_tour))
					$fields['tour'] = '';
				
				$this->notes = ArrayHelper::merge($this->notes, [
					'fields' => $fields,
				]);
			}
			else {
				$this->notes = ArrayHelper::merge($this->notes, [
					'fields' => [
						'project' => '',
						'phone' => '',
						'region' => '',
						'email' => '',
						'site' => '',
						'promo' => '',
						'category' => '',
						'tour' => '',
					],
				]);
			}
		}
		
		$test = false;
		if ($test) {
			$this->notes['fields']['project'] = 'AppMake';
			$this->notes['fields']['phone'] = '+79250011555';
			$this->notes['fields']['region'] = 1;
			$this->notes['fields']['email'] = 'safronov.ser@icloud.com';
			$this->notes['fields']['site'] = 'http://appmake.ru';
			$this->notes['fields']['promo'] = 'http://appmake.ru/promo';
			//$this->notes['fields']['category'] = 1;
		}
		
		foreach ($this->notes['fields'] as $key => $val) {
			if (is_null($val) || empty($val)) {
				$result = call_user_func([$this, 'askField'.ucfirst($key)]);
				if ($result) {
					return $result;
				}
			}
		}
		
		$result = $this->complete();
		if ($result)
			return $result;
		
		if ($this->notes['complete']) {
			$this->conversation->stop();
			
			if ($this->projects_count)
				return $this->getTelegram()->executeCommand('profile');
			
			$this->data['text'] = Yii::t('telegram-executor-signup', 'message_complete');
			$this->data['reply_markup'] = Answers::getProfileKeyboard();
			
			return Request::sendMessage($this->data);
		}
		
		
		return Request::emptyResponse();
	}
	
	protected function askFieldProject() {
		if (empty($this->text)) {
			$this->notes['state'] = 0;
			$this->conversation->update();
			
			$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_project'));
			return Request::sendMessage($this->data);
		}
		else {
			$this->saveField('project', $this->text);
		}
	}
	
	protected function askFieldPhone() {
		$isValidated = false;
		if ($this->text) {
			$this->text = preg_replace('/[^0-9+]/u','', $this->text);
			if (is_numeric($this->text)) {
				$pos = strpos($this->text, '8');
				if ($pos === 0)
					$this->text = substr_replace($this->text, '+7', $pos, strlen('8'));
				
				try {
					$parse = PhoneNumber::parse($this->text);
					$isValidated = $parse->isValidNumber();
				}
				catch (PhoneNumberParseException $e) {}
			}
		}
		
		if ((is_null($this->text) || (!is_null($this->text) && !$isValidated))) {
			$this->notes['state'] = 1;
			$this->conversation->update();
			
			$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_phone'));
			if (!empty($this->text))
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'error_phone_invalid'));
			
			//$this->data['reply_markup'] = (new Keyboard((new KeyboardButton(Yii::t('telegram-executor-signup', 'share_phone')))->setRequestContact(true)))->setOneTimeKeyboard(true)->setResizeKeyboard(true)->setSelective(true);
			return Request::sendMessage($this->data);
		}
		else {
			$val = ($this->text) ? PhoneNumber::parse($this->text)->format(PhoneNumberFormat::E164) : $this->message->getContact()->getPhoneNumber();
			
			$region = TelegramRegion::getRegionByPhone($val);
			if ($region) {
				$this->notes['region'] = [
					'id' => $region->id,
					'title' => $region->title,
				];
				$this->conversation->update();
			}
			
			$this->saveField('phone', $val);
		}
	}
	
	protected function askFieldRegion($reset = false) {
		if (!$reset && $this->getCallbackQuery()) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->getCallbackQuery()->getData(), $match)) {
				if (count($match) == 3) {
					if (in_array($match[2], ['region_confirm'])) {
						$this->saveField('region', $this->notes['region']['id']);
					}
					if (in_array($match[2], ['region_correct'])) {
						$this->notes['region'] = null;
						$this->conversation->update();
						
						$this->text = null;
						$this->callback_query = null;
						
						return $this->askFieldRegion(true);
						
					}
				}
			}
		}
		else if (strlen($this->text)) {
			$found = TelegramRegion::findRegionByText($this->text);
			if ($found) {
				$distance = (int)$found['distance'];
				if ($distance) {
					
					$this->notes['region'] = $found;
					$this->conversation->update();
					
					$this->data['text'] = Yii::t('telegram-executor-signup', 'message_region_found_poor', ['region' => $found['title']]);
					$this->data['reply_markup'] = [
						'inline_keyboard' => [
							[
								['text' => Yii::t('telegram-executor-signup', 'button_region_confirm'), 'callback_data' => '/signup action:region_confirm'],
								['text' => Yii::t('telegram-executor-signup', 'button_region_correct'), 'callback_data' => '/signup action:region_correct'],
							],
						],
					];
					
					return Request::sendMessage($this->data);
				}
				else {
					$this->saveField('region', $found['id']);
				}
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'error_region_invalid'));
				return Request::sendMessage($this->data);
			}
		}
		else {
			$this->notes['state'] = 2;
			
			if ($this->notes['region']) {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_region_found', ['region' => $this->notes['region']['title']]));
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[
							['text' => Yii::t('telegram-executor-signup', 'button_region_confirm'), 'callback_data' => '/signup action:region_confirm'],
							['text' => Yii::t('telegram-executor-signup', 'button_region_correct'), 'callback_data' => '/signup action:region_correct'],
						],
					],
				];
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_region'));
			}
			
			return Request::sendMessage($this->data);
		}
	}
	
	protected function askFieldEmail() {
		if (empty($this->text) || !(new EmailValidator(['enableIDN' => true]))->validate($this->text)) {
			$this->notes['state'] = 3;
			$this->conversation->update();
			
			if (empty($this->text)) {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_email'));
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'error_email_invalid'));
			}
			return Request::sendMessage($this->data);
			
		}
		else {
			$this->saveField('email', $this->text);
		}
	}
	
	protected function askFieldSite() {
		if (empty($this->text) || !(new UrlValidator(['defaultScheme' => 'http', 'enableIDN' => true]))->validate($this->text)) {
			$this->notes['state'] = 4;
			$this->conversation->update();
			
			if (empty($this->text)) {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_site'));
			}
			else {
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'error_site_invalid'));
			}
			return Request::sendMessage($this->data);
			
		}
		else {
			if ($this->text && strpos($this->text, 'http') === false)
				$this->text = 'http://'.$this->text;
			$this->saveField('site', $this->text);
		}
	}
	
	protected function askFieldPromo() {
		if ($this->callback_query) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->callback_query->getData(), $match)) {
				if (count($match) == 3) {
					if ($match[1] == 'promo' && $match[2] == 'no') {
						$this->saveField('promo', 'skip');
						$this->callback_query = null;
					}
				}
			}
		}
		else {
			if (empty($this->text) || !(new UrlValidator(['defaultScheme' => 'http', 'enableIDN' => true]))->validate($this->text)) {
				$this->notes['state'] = 5;
				$this->conversation->update();
				
				if (empty($this->text)) {
					$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_promo'));
				}
				else {
					$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'error_promo_invalid'));
				}
				
				$this->data['reply_markup'] = (new InlineKeyboard([
					['text' => Yii::t('telegram-executor-signup', 'button_skip'),  'callback_data' => 'promo:no'],
				]))->setOneTimeKeyboard(true)->setResizeKeyboard(true)->setSelective(true);
				
				return Request::sendMessage($this->data);
			}
			else {
				if ($this->text && strpos($this->text, 'http') === false)
					$this->text = 'http://'.$this->text;
				$this->saveField('promo', $this->text);
			}
		}
	}
	
	protected function askFieldCategory() {
		if ($this->callback_query) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->callback_query->getData(), $match)) {
				if (count($match) == 3) {
					if ($match[1] == 'category') {
						$this->saveField('category', $match[2]);
						$this->callback_query = null;
					}
				}
			}
		}
		else {
			$this->notes['state'] = 6;
			$this->conversation->update();
			
			$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_category'));
			$this->data['reply_markup'] = ['inline_keyboard' => $this->getCategoryKeyboard()];
			
			return Request::sendMessage($this->data);
		}
	}
	
	protected function askFieldTour() {
		if ($this->callback_query) {
			if (preg_match('/^([^:]+)(?:\:(.+))?/', $this->getCallbackQuery()->getData(), $match)) {
				if (count($match) == 3) {
					if (in_array($match[2], ['tour_yes', 'tour_no'])) {
						$isTour = ($match[2] == 'tour_yes') ? 1 : 0;
						$this->saveField('tour', $isTour);
					}
				}
			}
		}
		else {
			$category = null;
			if ($this->projects_count)
				$category = $this->getUser()->getProjects()->one()->category;
			else if (isset($this->notes['fields']['category']))
				$category = TelegramCategory::findBy($this->notes['fields']['category']);
			if ($category && $category->getIsArea()) {
				$this->saveField('tour', 0);
			}
			else {
				$this->notes['state'] = 7;
				$this->conversation->update();
				
				$this->data['text'] = $this->getFieldTextPrefix(Yii::t('telegram-executor-signup', 'tip_tour'));
				$this->data['reply_markup'] = [
					'inline_keyboard' => [
						[['text' => Yii::t('telegram-executor-signup', 'button_tour_yes'), 'callback_data' => '/signup action:tour_yes']],
						[['text' => Yii::t('telegram-executor-signup', 'button_tour_no'), 'callback_data' => '/signup action:tour_no']],
					],
				];
				
				return Request::sendMessage($this->data);
			}
		}
	}
	
	protected function complete() {
		if ($this->notes['complete'])
			return;
		
		/** @var TelegramProject $project */
		$project = $this->save();
		if ($project) {
			$this->notes['state'] = 8;
			$this->notes['complete'] = true;
			$this->conversation->update();
			
			$user = $this->getUser();
			
			$sites = $project->getAttribute('site');
			if ($project->getAttribute('promo'))
				$sites .= PHP_EOL.$project->getAttributeLabel('promo').': '.$project->getAttribute('promo');
			
			$contacts = '';
			$contacts .= $user->getAttributeLabel('region_id').': '.$user->region->title.PHP_EOL;
			$contacts .= $user->getAttributeLabel('phone').': '.$user->getPhoneFormatted().PHP_EOL;
			$contacts .= $user->getAttributeLabel('email').': '.$user->getAttribute('email').PHP_EOL;
			if ($user->username)
				$contacts .= Yii::t('telegram-customer-answer', 'message_answer_telegram', ['username' => $user->username]).PHP_EOL;
			$contacts .= $user->getTour().PHP_EOL;
			
			$text = Yii::t('telegram-executor-signup', 'message_confirm').PHP_EOL.PHP_EOL;
			$text .= Yii::t('telegram-executor-signup', 'message_confirm_info', [
				'project' => $project->title,
				'fullname' => $project->user->getFullname(),
				'sites' => $sites,
				'contacts' => $contacts,
			]);
			
			$this->data['text'] = $text;
			
			Request::sendMessage($this->data);
		}
		else {
			$this->notes['complete'] = true;
			$this->conversation->update();
		}
	}
	
	protected function getFieldTextPrefix($text) {
		$state = 0;
		foreach ($this->notes['fields'] as $field) {
			if (!empty($field))
				$state++;
		}
		return Answers::stepTextPrefix($text, $state, count($this->notes['fields']));
	}
	
	protected function getCategories() {
		$module = Yii::$app->getModule('telegram');
		return $module->categories;
	}
	
	protected function getCategoryKeyboard() {
		$keyboard = [];
		foreach ($this->getCategories() as $id => $name) {
			$keyboard[] = [new InlineKeyboardButton([
				'text' => $name.($this->isSelectedCategory($id) ? ' ✓' : ' '),
				'callback_data' => 'category:'.$id,
			])];
		}
		return $keyboard;
	}
	
	protected function save() {
		$user = $this->getUser();
		$fields = $this->notes['fields'];
		if (!$this->projects_count && isset($fields['category'])) {
			$project = TelegramProject::find()->where(['telegram_user_id' => $this->getUserId()])->one();
			if (!$project)
				$project = new TelegramProject();
			$project->category_id = $fields['category'];
			$project->title = $fields['project'];
			$project->site = $fields['site'];
			$project->promo = ($fields['promo'] != 'skip') ? $fields['promo'] : '';
			
			$project->status = Status::ENABLED;
			if ($project->validate()) {
				$user->link('projects', $project);
				
				$user->phone = $fields['phone'];
				$user->email = $fields['email'];
				$user->region_id = (integer)$fields['region'];
				$user->is_tour = (bool)$fields['tour'];
				$user->save();
				
				return $project;
			}
		}
		else {
			if (isset($fields['phone']))
				$user->phone = $fields['phone'];
			
			if (isset($fields['email']))
				$user->email = $fields['email'];
			
			if (isset($fields['region']))
				$user->region_id = $fields['region'];
			
			if (isset($fields['tour']))
				$user->is_tour = $fields['tour'];
			$user->save();
		}
		return false;
	}
	
	protected function saveField($field, $val) {
		$this->notes['fields'][$field] = $val;
		$this->text = null;
		$this->conversation->update();
	}
	
	protected function isSelectedCategory($id) {
		return $this->notes['category'] == $id;
	}
}