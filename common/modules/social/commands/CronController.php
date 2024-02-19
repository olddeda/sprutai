<?php
namespace common\modules\social\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\StringHelper;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\content\models\Content;
use common\modules\content\models\Article;
use common\modules\content\models\News;
use common\modules\content\models\Portfolio;
use common\modules\content\helpers\enum\Type;

use common\modules\plugin\models\Plugin;

use common\modules\media\models\Media;
use common\modules\media\components\Image;

use common\modules\tag\helpers\enum\Type as TagType;
use common\modules\tag\models\Tag;

use common\modules\social\models\SocialItem;

use common\modules\telegram\models\TelegramChat;

use common\modules\catalog\models\CatalogItem;

/**
 * Class Cron
 * @package common\modules\social\commands
 */

class CronController extends Controller
{
	/** @var string  */
	protected $chatChannelId = '@SprutAI_News';

	public function actionIndex() {

		if (Yii::$app->settings->get('telegram', 'article'))
			$this->actionArticle();

		if (Yii::$app->settings->get('telegram', 'news'))
			$this->actionNews();

		if (Yii::$app->settings->get('telegram', 'plugin'))
			$this->actionPlugins();

		if (Yii::$app->settings->get('telegram', 'portfolio'))
			$this->actionPortfolio();

		//$this->actionCatalogItem();
	}

	public function actionArticle() {

		$id = Yii::$app->db->createCommand('
			SELECT t.id
			FROM '.Article::tableName().' AS t
			LEFT JOIN '.SocialItem::tableName().' AS si
			ON si.module_type = :module_type
			AND si.module_id = t.id
			WHERE t.status = 1
			AND si.id IS NULL
			AND t.type = :type
			AND t.notification = 1
			ORDER BY t.created_at ASC
			LIMIT 1
		', [
			':module_type' => ModuleType::CONTENT,
			':type' => Type::ARTICLE,
 		])->queryScalar();

		if ($id) {

			/** @var Article $model */
			$model = Article::findById($id);

			// Get image
			$image = $this->_getImage($model);

			$url = 'https://sprut.ai/client/article/'.$id.'?utm_source=telegram&utm_medium=telegram&utm_campaign=article';

			$text = '<b>'.$model->title.'</b>'.PHP_EOL;
			$text.= $model->owner->type.': <b>';
			if ($model->owner->isCompany)
				$text.= $model->owner->title;
			else
				$text.= $model->author->getAuthorName(true);
			$text.= '</b>';

			if (strlen($model->descr)) {
				$textLength = mb_strlen($text);
				$descrLength = 200 - $textLength;

				$text .= PHP_EOL.PHP_EOL.StringHelper::truncate($model->descr, $descrLength);
			}

			$params = [
				'chat_id' => $this->chatChannelId,
				'parse_mode' => 'HTML',
				'reply_markup' => Json::encode([
					'inline_keyboard' => [
						[
							['text' => 'Читать статью', 'url' => $url],
						]
					],
				]),
			];

			if ($this->_mailing($params, $image, $text, $model)) {
				Yii::$app->db->createCommand('
					INSERT INTO am_social_item (module_type, module_id, post_telegram_at, created_by, updated_by, created_at, updated_at)
					VALUES (:module_type, :module_id, :post_telegram_at, :created_by, :updated_by, :created_at, :updated_at)
				', [
					':module_type' => ModuleType::CONTENT,
					':module_id' => $id,
					':post_telegram_at' => time(),
					':created_by' => 1,
					':updated_by' => 1,
					':created_at' => time(),
					':updated_at' => time(),
				])->execute();
			}
		}

	}

	public function actionNews() {

		$id = Yii::$app->db->createCommand('
			SELECT t.id
			FROM '.Article::tableName().' AS t
			LEFT JOIN '.SocialItem::tableName().' AS si
			ON si.module_type = :module_type
			AND si.module_id = t.id
			WHERE t.status = 1
			AND si.id IS NULL
			AND t.type = :type
			ORDER BY t.created_at ASC
			LIMIT 1
		', [
			':module_type' => ModuleType::CONTENT,
			':type' => Type::NEWS,
		])->queryScalar();

		if ($id) {

			/** @var News $model */
			$model = News::findById($id);

			// Get image
			$image = $this->_getImage($model);

			$url = 'https://sprut.ai/client/news/'.$id.'?utm_source=telegram&utm_medium=telegram&utm_campaign=news';

			$text = '<b>'.$model->title.'</b>'.PHP_EOL;
			$text.= $model->owner->type.': <b>';
			if ($model->owner->isCompany)
				$text.= $model->owner->title;
			else
				$text.= $model->author->getAuthorName(true);
			$text.= '</b>';

			if (strlen($model->descr)) {
				$textLength = mb_strlen($text);
				$descrLength = 1024 - $textLength;

				$text .= PHP_EOL.PHP_EOL.StringHelper::truncate($model->descr, $descrLength);
			}

			$params = [
				'chat_id' => $this->chatChannelId,
				'parse_mode' => 'HTML',
				'reply_markup' => Json::encode([
					'inline_keyboard' => [
						[
							['text' => 'Читать новость', 'url' => $url],
						]
					],
				]),
			];

			if ($this->_mailing($params, $image, $text, $model)) {
				Yii::$app->db->createCommand('
					INSERT INTO am_social_item (module_type, module_id, post_telegram_at, created_by, updated_by, created_at, updated_at)
					VALUES (:module_type, :module_id, :post_telegram_at, :created_by, :updated_by, :created_at, :updated_at)
				', [
					':module_type' => ModuleType::CONTENT,
					':module_id' => $id,
					':post_telegram_at' => time(),
					':created_by' => 1,
					':updated_by' => 1,
					':created_at' => time(),
					':updated_at' => time(),
				])->execute();
			}
		}

	}

	public function actionPlugins() {

		$id = Yii::$app->db->createCommand('
			SELECT t.id
			FROM '.Plugin::tableName().' AS t
			LEFT JOIN '.SocialItem::tableName().' AS si
			ON si.module_type = :module_type
			AND si.module_id = t.id
			WHERE t.status = 1
			AND si.id IS NULL
			AND t.type = :type
			ORDER BY t.created_at ASC
			LIMIT 1
		', [
			':module_type' => ModuleType::CONTENT,
			':type' => Type::PLUGIN,
		])->queryScalar();

		if ($id) {

			/** @var News $model */
			$model = Plugin::findById($id);

			// Get image
			$image = $this->_getImage($model);

			$url = 'https://sprut.ai/client/plugins/'.$id.'?utm_source=telegram&utm_medium=telegram&utm_campaign=plugins';

			$text = '<b>'.$model->title.'</b>'.PHP_EOL;
			$text.= $model->owner->type.': <b>';
			if ($model->owner->isCompany)
				$text.= $model->owner->title;
			else
				$text.= $model->author->getAuthorName(true);
			$text.= '</b>';

			if (strlen($model->descr)) {
				$textLength = mb_strlen($text);
				$descrLength = 1024 - $textLength;

				$text .= PHP_EOL.PHP_EOL.StringHelper::truncate($model->descr, $descrLength);
			}

			$params = [
				'chat_id' => $this->chatChannelId,
				'parse_mode' => 'HTML',
				'reply_markup' => Json::encode([
					'inline_keyboard' => [
						[
							['text' => 'Смотреть плагин', 'url' => $url],
						]
					],
				]),
			];

			if ($this->_mailing($params, $image, $text, $model)) {
				Yii::$app->db->createCommand('
					INSERT INTO am_social_item (module_type, module_id, post_telegram_at, created_by, updated_by, created_at, updated_at)
					VALUES (:module_type, :module_id, :post_telegram_at, :created_by, :updated_by, :created_at, :updated_at)
				', [
					':module_type' => ModuleType::CONTENT,
					':module_id' => $id,
					':post_telegram_at' => time(),
					':created_by' => 1,
					':updated_by' => 1,
					':created_at' => time(),
					':updated_at' => time(),
				])->execute();
			}
		}

	}

	public function actionPortfolio() {

		$id = Yii::$app->db->createCommand('
			SELECT t.id
			FROM '.Article::tableName().' AS t
			LEFT JOIN '.SocialItem::tableName().' AS si
			ON si.module_type = :module_type
			AND si.module_id = t.id
			WHERE t.status = 1
			AND si.id IS NULL
			AND t.type = :type
			ORDER BY t.created_at ASC
			LIMIT 1
		', [
			':module_type' => ModuleType::CONTENT,
			':type' => Type::PORTFOLIO,
		])->queryScalar();

		if ($id) {

			/** @var Portfolio $model */
			$model = Portfolio::findById($id);

			// Get image
			$image = $this->_getImage($model);

			$url = 'https://sprut.ai/client/companies/portfolio/'.$model->company_id.'/'.$id.'?utm_source=telegram&utm_medium=telegram&utm_campaign=portfolio';

			$text = '<b>'.$model->title.'</b>'.PHP_EOL;
			$text.= $model->owner->type.': <b>';
			if ($model->owner->isCompany)
				$text.= $model->owner->title;
			else
				$text.= $model->author->getAuthorName(true);
			$text.= '</b>';

			if (strlen($model->descr)) {
				$textLength = mb_strlen($text);
				$descrLength = 1024 - $textLength;

				$text .= PHP_EOL.PHP_EOL.StringHelper::truncate($model->descr, $descrLength);
			}

			$params = [
				'chat_id' => $this->chatChannelId,
				'parse_mode' => 'HTML',
				'reply_markup' => Json::encode([
					'inline_keyboard' => [
						[
							['text' => 'Смотреть работу', 'url' => $url],
						]
					],
				]),
			];

			if ($this->_mailing($params, $image, $text, $model)) {
				Yii::$app->db->createCommand('
					INSERT INTO am_social_item (module_type, module_id, post_telegram_at, created_by, updated_by, created_at, updated_at)
					VALUES (:module_type, :module_id, :post_telegram_at, :created_by, :updated_by, :created_at, :updated_at)
				', [
					':module_type' => ModuleType::CONTENT,
					':module_id' => $id,
					':post_telegram_at' => time(),
					':created_by' => 1,
					':updated_by' => 1,
					':created_at' => time(),
					':updated_at' => time(),
				])->execute();
			}
		}

	}

    public function actionCatalogItem() {
        $id = Yii::$app->db->createCommand('
			SELECT t.id
			FROM '.CatalogItem::tableName().' AS t
			LEFT JOIN '.SocialItem::tableName().' AS si
			ON si.module_type = :module_type
			AND si.module_id = t.id
			WHERE t.status = 1
			AND si.id IS NULL
			ORDER BY t.created_at ASC
			LIMIT 1
		', [
            ':module_type' => ModuleType::CATALOG_ITEM,
        ])->queryScalar();

        if ($id) {
            /** @var CatalogItem $model */
            $model = CatalogItem::findById($id);

            // Get image
            $image = $this->_getImage($model);

            $catalogUrl = 'https://v2.sprut.ai/catalog?utm_source=telegram&utm_medium=telegram&utm_campaign=catalog';
            $catalogItemUrl = 'https://v2.sprut.ai/catalog/item/'.$model->seo->slugify.'?utm_source=telegram&utm_medium=telegram&utm_campaign=catalog_item';

            $text = Yii::t('notification', 'catalog_item_new', [
                'title' => $model->vendor->title.' '.$model->title,
                'catalog_url' => $catalogUrl,
                'catalog_item_url' => $catalogItemUrl,
            ]);

            $text .= PHP_EOL;
            if ($model->model) {
                $text .= PHP_EOL.'<b>Производитель:</b> '.$model->vendor->title;
                $text .= PHP_EOL.'<b>Модель:</b> '.$model->model;
            }

            $types = Tag::find()->where([
                'AND',
                ['in', 'id', $model->tags_ids],
                ['&', 'type', TagType::TYPE],
            ])->all();
            if ($types) {
                $t = implode(', ', ArrayHelper::getColumn($types, 'title'));
                $text .= PHP_EOL.'<b>Тип устройства:</b> '.$t;
            }

            $platforms = Tag::find()->where([
                'AND',
                ['in', 'id', $model->tags_ids],
                ['&', 'type', TagType::PLATFORM],
            ])->all();
            if ($platforms) {
                $t = implode(', ', ArrayHelper::getColumn($platforms, 'title'));
                $text .= PHP_EOL.'<b>Платформа:</b> '.$t;
            }

            $protocols = Tag::find()->where([
                'AND',
                ['in', 'id', $model->tags_ids],
                ['&', 'type', TagType::PROTOCOL],
            ])->all();
            if ($protocols) {
                $t = implode(', ', ArrayHelper::getColumn($protocols, 'title'));
                $text .= PHP_EOL.'<b>Протокол:</b> '.$t;
            }

            //print_r($text);die;

            $params = [
                'chat_id' => -1001082506583,
                'parse_mode' => 'HTML',
                'reply_markup' => Json::encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Смотреть устройство', 'url' => $catalogItemUrl],
                        ]
                    ],
                ]),
            ];

            if ($this->_mailing($params, $image, $text, $model, false)) {
                Yii::$app->db->createCommand('
					INSERT INTO am_social_item (module_type, module_id, post_telegram_at, created_by, updated_by, created_at, updated_at)
					VALUES (:module_type, :module_id, :post_telegram_at, :created_by, :updated_by, :created_at, :updated_at)
				', [
                    ':module_type' => ModuleType::CATALOG_ITEM,
                    ':module_id' => $id,
                    ':post_telegram_at' => time(),
                    ':created_by' => 1,
                    ':updated_by' => 1,
                    ':created_at' => time(),
                    ':updated_at' => time(),
                ])->execute();
            }
        }
    }

	/**
	 * @param $params
	 * @param $image
	 *
	 * @return bool
	 */
	private function _mailing($params, $image, $text, $model, $useTagsIds = true) {

		/** @var \common\modules\media\Module $module */
		$module = Yii::$app->getModule('media');

		/** @var \creocoder\flysystem\Filesystem $fs */
		$fs = $module->fs;

		$isPhoto = false;

		$fileExists = true;
		if ($fs instanceof \common\modules\base\components\flysystem\LocalFilesystem)
			$fileExists = file_exists($image);

		if ($image && $fileExists) {
			$tmpPath = '/tmp/'.time();

			/** @var \common\modules\media\components\Image $img */
			$img = new Image();
			$img->load(file_get_contents($image));
			$img->resize(2000	, 2000);
			file_put_contents($tmpPath, $img->get());

			$params['caption'] = $text;
			$params['photo'] = $tmpPath;

			$isPhoto = true;
		}
		else {
			$params['text'] = $text;
		}

		$response = $this->_send($params, $isPhoto);
		if (is_object($response) && $response->ok) {

            if ($useTagsIds) {
                $messageChannelId = $response->result->message_id;

                $params['reply_markup'] = Json::encode([
                    'inline_keyboard' => [
                        [
                            ['text' => 'Читать канал', 'url' => 'https://t.me/SprutAI_News/'.$messageChannelId],
                        ]
                    ],
                ]);


                $chatIds = [];
                $tagsIds = ArrayHelper::getColumn($model->getTags()->andFilterCompare(Tag::tableName().'.type', TagType::SYSTEM, '&')->all(), 'id');
                if (count($tagsIds)) {
                    $chatIds = ArrayHelper::merge($chatIds, TelegramChat::getIdentifiersContent($tagsIds));
                }

                $chatIds = array_unique($chatIds);
                foreach ($chatIds as $forwardChatId) {
                    $params['chat_id'] = (int)$forwardChatId;
                    $this->_send($params, $isPhoto);
                }
            }

			return true;
		}
		return false;
	}

	/**
	 * @param array $params
	 * @param bool $isPhoto
	 *
	 * @return mixed
	 */
	private function _send(array $params, bool $isPhoto) {

		/** @var \common\modules\base\extensions\telegram\Telegram $telegram */
		$telegram = Yii::$app->telegram;

		return $isPhoto ? $telegram->sendPhoto($params) : $telegram->sendMessage($params);
	}

	/**
	 * Get image
	 *
	 * @param Content $model
	 *
	 * @return null|string
	 */
	private function _getImage($model) {

		/** @var \common\modules\media\Module $module */
		$module = Yii::$app->getModule('media');

		/** @var \creocoder\flysystem\Filesystem $fs */
		$fs = $module->fs;

		$image = null;
		if ($model->image && $model->image->mediaImage) {
			if ($fs instanceof \common\modules\base\components\flysystem\AwsS3Filesystem)
				$image = $fs->url.DIRECTORY_SEPARATOR.$model->image->mediaImage->getFilePath(true).$model->image->mediaImage->getFile();
			else
				$image = $fs->path.DIRECTORY_SEPARATOR.$model->image->mediaImage->getFilePath(true).$model->image->mediaImage->getFile();
		}

		return $image;
	}
}