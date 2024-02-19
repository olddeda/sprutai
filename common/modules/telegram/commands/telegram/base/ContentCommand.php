<?php
namespace Longman\TelegramBot\Commands\AdminCommands;

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
 * Content command
 */
class ContentCommand extends AdminCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'content';
	
	/**
	 * @var string
	 */
	protected $description = 'Отобразить информацию по материалу';
	
	/**
	 * @var string
	 */
	protected $usage = '/content <id>';
	
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
	 * @throws \ReflectionException
	 */
	public function execute() {
		
		Request::sendChatAction([
			'chat_id' => $this->getChatId(),
			'action'  => ChatAction::TYPING,
		]);
		
		$params = [
			'chat_id' => $this->getChatId(),
			'parse_mode' => 'HTML',
			'text' => Yii::t('telegram-base-content', 'message_empty'),
		];
		
		$contentID = (int)$this->getText();
		if ($contentID) {
			$model = Content::find()->where(['id' => $contentID])->one();
			if (!is_null($model)) {
			
				
				$text = '<b>'.Type::getLabel($model->type).': '.$model->title.'</b>';
				if ($model->descr)
					$text .= PHP_EOL.PHP_EOL.'<i>'.$model->descr.'</i>';
				$text .= PHP_EOL.PHP_EOL.Yii::t('telegram-base-content', 'message_author', ['author' => $model->author->getAuthorName(true)]);
				
				$urlView = Url::base('https');
				if (in_array($model->type, [Type::ARTICLE, Type::NEWS, Type::BLOG]))
					$urlView .= '/content/'.$model->getUriModuleName().'/'.$model->id;
				else if ($model->type == Type::PROJECT)
					$urlView .= '/project/default/'.$model->id;
				else if ($model->type == Type::PLUGIN)
					$urlView .= '/plugin/default/'.$model->id;
				
				
				$urlEdit = Url::base('https');
				if (in_array($model->type, [Type::ARTICLE, Type::NEWS, Type::BLOG]))
					$urlEdit .= '/content/'.$model->getUriModuleName().'/update/'.$model->id;
				else if ($model->type == Type::PROJECT)
					$urlEdit .= '/project/default/update/'.$model->id;
				else if ($model->type == Type::PLUGIN)
					$urlEdit .= '/plugin/default/update/'.$model->id;
				
				$urlAuthorProfile = Url::base('https').'/user/profile/'.$model->author_id;
				
				$params['text'] = $text;
				$params['reply_markup'] = new InlineKeyboard([
					['text' => Yii::t('telegram-base-content', 'button_view'), 'url' => $urlView],
					['text' => Yii::t('telegram-base-content', 'button_update'), 'url' => $urlEdit],
				],[
					['text' => Yii::t('telegram-base-content', 'button_author_profile'), 'url' => $urlAuthorProfile],
				]);
			}
			else {
				$params['text'] = Yii::t('telegram-base-content', 'message_not_found');
			}
		}
		
		
		$res = Request::sendMessage($params);
		
		if (!$res->isOk())
			Helpers::dump($res->printError(true));
	}
}