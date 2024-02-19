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
 * Whoami command
 */
class WhoamiCommand extends UserCommand {
	
	/**
	 * @var string
	 */
	protected $name = 'whoami';
	
	/**
	 * @var string
	 */
	protected $description = 'Рассказать о себе';
	
	/**
	 * @var string
	 */
	protected $usage = '/whoami';
	
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
		
		Request::sendChatAction([
			'chat_id' => $this->getChatId(),
			'action'  => ChatAction::TYPING,
		]);
		
		$params = [
			'chat_id' => $this->getChatId(),
			'reply_to_message_id' => $this->getMessage()->getMessageId(),
			'parse_mode' => 'HTML',
			'text' => Yii::t('telegram-base-whoami', 'message_not_found'),
		];
		
		/** @var \common\modules\media\Module $module */
		$module = Yii::$app->getModule('media');
		
		/** @var \creocoder\flysystem\LocalFilesystem $fs */
		$fs = $module->fs;
		
		$userTelegram = $this->getUser();
		if ($userTelegram && $userTelegram->getIsConnected()) {
			
			/** @var \common\modules\user\models\User $user */
			$user = $userTelegram->getUser()->votes()->one();
			
			$image = null;
			if ($user->avatar && $user->avatar->mediaImage) {
				if ($fs instanceof \common\modules\base\components\flysystem\AwsS3Filesystem)
					$image = $fs->url.DIRECTORY_SEPARATOR.$user->avatar->mediaImage->getFilePath(true).$user->avatar->mediaImage->getFile();
				else
					$image = $fs->path.DIRECTORY_SEPARATOR.$user->avatar->mediaImage->getFilePath(true).$user->avatar->mediaImage->getFile();
			}
			
			$fileExists = true;
			if ($fs instanceof \common\modules\base\components\flysystem\LocalFilesystem)
				$fileExists = file_exists($image);
			
			if ($image && $fileExists) {
				$tmpPath = '/tmp/'.time();
				
				/** @var \common\modules\media\components\Image $img */
				$img = new Image();
				$img->load(file_get_contents($image));
				$img->resize(600	, 600);
				file_put_contents($tmpPath, $img->get());
				
				$params['photo'] = Request::encodeFile($tmpPath);
			}
			else {
				$params['photo'] = 'https://sprut.ai/static/media/cache/placeholder/client/600x600xc_placeholder_1000x600.jpg';
			}
			
			$text = '<b>'.$user->getAuthorName().'</b>';
			
			/** @var ContentAuthorStat $stat */
			$stat = $user->contentsStat;
			if ($stat) {
				$tmp = [];
				if ($stat->articles)
					$tmp[] = Yii::t('content-author-stat', 'count_articles', ['n' => $stat->articles]);
				if ($stat->news)
					$tmp[] = Yii::t('content-author-stat', 'count_news', ['n' => $stat->news]);
				if ($stat->blogs)
					$tmp[] = Yii::t('content-author-stat', 'count_blogs', ['n' => $stat->blogs]);
				if ($stat->projects)
					$tmp[] = Yii::t('content-author-stat', 'count_projects', ['n' => $stat->projects]);
				if ($stat->plugins)
					$tmp[] = Yii::t('content-author-stat', 'count_plugins', ['n' => $stat->plugins]);
				if (count($tmp))
					$text .= PHP_EOL.implode(', ', $tmp);
			}
			
			if ($user->userFavoritePositive) {
				$text .= PHP_EOL.'Подписчиков '.$user->userFavoritePositive;
			}
			
			
			$params['caption'] = $text;
			
			$url = 'https://sprut.ai/client/user/profile/'.$user->id;
			$params['reply_markup'] = new InlineKeyboard([
				['text' => 'Перейти в профиль', 'url' => $url],
			]);
			
			$result = Request::sendPhoto($params);
			
			return $result;
		}
		
		return Request::sendMessage($params);
	}
}