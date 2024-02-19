<?php
namespace common\modules\notification\providers;

use Yii;
use yii\base\Exception;

use creocoder\flysystem\Filesystem;

use common\modules\base\components\flysystem\AwsS3Filesystem;
use common\modules\base\components\flysystem\LocalFilesystem;

use common\modules\media\Module;
use common\modules\media\components\Image;

use common\modules\notification\components\NotificationEvent;
use common\modules\notification\components\Provider;
use common\modules\base\extensions\telegram\Telegram;
use yii\helpers\ArrayHelper;

/**
 * Class TelegramProvider
 * @package common\modules\notification\providers
 */
class TelegramProvider extends Provider
{
	public function send(NotificationEvent $notification) {
		if (empty($notification->toId))
			return;

		$settings = is_array($notification->params) ? $notification->params : [];

		$bot = ArrayHelper::getValue($settings, 'bot', 'telegram');

        /** @var Telegram $telegram */
		$telegram = Yii::$app->get($bot);
		if (!$telegram)
			throw new Exception();
		
		$toIds = (is_array($notification->toId)) ? $notification->toId : [$notification->toId];
		
		$idx = 0;
		$time = time();
		foreach ($toIds as $toId) {
			if ($idx++ == 30 && (time() - $time) > 1) {
				sleep(1);
				$idx = 0;
				$time = time();
			}

			$params = [
                'chat_id' => $toId,
                'text' => $notification->message,
                'parse_mode' => 'HTML',
            ];

			$isPhoto = false;
			if (is_array($notification->params) && isset($notification->params['image'])) {

                /** @var Module $module */
                $module = Yii::$app->getModule('media');

                /** @var Filesystem $fs */
                $fs = $module->fs;

                $image = $this->_getImagePath($notification->params['image']);

                $fileExists = true;
                if ($fs instanceof LocalFilesystem) $fileExists = file_exists($image);

                if ($image && $fileExists) {
                    $tmpPath = '/tmp/'.time();

                    /** @var Image $img */
                    $img = new Image();
                    $img->load(file_get_contents($image));
                    $img->resize(2000, 2000);
                    file_put_contents($tmpPath, $img->get());

                    $params['caption'] = $notification->message;
                    $params['photo'] = $tmpPath;
                    unset($params['text']);

                    $isPhoto = true;
                }
            }

            $response = $isPhoto ? $telegram->sendPhoto($params) : $telegram->sendMessage($params);
			
			$this->status[$toId] = (is_object($response) && $response->ok);
		}
	}

    /**
     * @param string $image
     *
     * @return null|string
     */
    private function _getImagePath(string $image) {

        /** @var Module $module */
        $module = Yii::$app->getModule('media');

        /** @var Filesystem $fs */
        $fs = $module->fs;

        $imagePath = null;
        if ($image) {
            if ($fs instanceof AwsS3Filesystem)
                $imagePath = $fs->url.DIRECTORY_SEPARATOR.$image;
            else
                $imagePath = $fs->path.DIRECTORY_SEPARATOR.$image;
        }
        return $imagePath;
    }
}