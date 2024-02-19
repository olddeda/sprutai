<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use common\modules\catalog\models\CatalogItem;
use common\modules\content\models\Article;
use common\modules\media\helpers\enum\Mode;
use common\modules\telegram\helpers\Helpers;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\sphinx\Query;

/**
 * Inline query command
 *
 * Command that handles inline queries.
 */
class InlinequeryCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'inlinequery';
	
	/**
	 * @var string
	 */
	protected $description = 'Reply to inline query';
	
	/**
	 * @var string
	 */
	protected $version = '1.1.1';
	
	/**
	 * Command execute method
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute() {
		$inline_query = $this->getInlineQuery();
		$query = $inline_query->getQuery();
		
		$data = [
		    'inline_query_id' => $inline_query->getId()
        ];

		$results = [];

        if ($query !== '') {
            $ids = (new Query())
                ->select('id')
                ->from('idx_catalog_item')
                ->match($query)
                ->groupBy('id')
                ->column()
            ;

            /** @var CatalogItem $models */
            $models = CatalogItem::find()
                ->joinWith([
                    'media',
                    'platforms',
                    'protocols',
                    'types'
                ])
                ->with([
                    'vendor' => function($query) {
                        $query->alias('vendor')->with([
                            'links'
                        ]);
                    },
                    'tags' => function($query) {
                        $query->alias('tags')->with([
                            'links' => function ($query) {
                                $query->with([
                                    'links'
                                ]);
                            }
                        ]);
                    },
                    'stat',
                ])
                ->andWhere(['in', CatalogItem::tableName().'.id', $ids])
                ->groupBy(CatalogItem::tableName().'.id')
                ->limit(10)
                ->all()
            ;

            /** @var CatalogItem $model */
            foreach ($models as $model) {
                $tagPair = $this->_findTagById($model, 771);
                $ratingValue = $model->stat ? $model->stat->rating : 0;
                $commentsValue = $model->stat ? $model->stat->comments : 0;
                $rating = str_repeat('⭐', $ratingValue);
                $supportSprutHub = in_array(94, $model->tags_ids);

                $description = "";
                $tmp = [];
                if ($model->types) {
                    $types = ArrayHelper::getColumn($model->types, 'title');
                    sort($types);
                    $tmp[] = implode(', ', $types);
                }
                if ($model->protocols) {
                    $protocols = ArrayHelper::getColumn($model->protocols, 'title');
                    sort($protocols);
                    $tmp[] = implode(', ', $protocols);
                }
                if ($model->platforms) {
                    $platforms = ArrayHelper::getColumn($model->platforms, 'title');
                    sort($platforms);
                    $tmp[] = implode(', ', $platforms);
                }
                $description .= implode(', ', $tmp).PHP_EOL;

                $tmp = [];
                if ($ratingValue) {
                    $tmp[] = $rating;
                }
                $description .= implode(' ', $tmp);

                $messageText = "";
                if ($model->image && $model->image->getFileExists()) {
                    $messageText .= '<a href="'.$model->image->getImageSrc('1000', '1000', Mode::RESIZE).'">&#8205;</a>';
                }
                $messageText .= '<b>'.$model->getTitle_vendor_model().'</b>'.PHP_EOL;
                if ($ratingValue) {
                    $messageText .= $rating.PHP_EOL;
                }
                $messageText .= PHP_EOL;

                if ($model->types) {
                    $types = ArrayHelper::getColumn($model->types, 'title');
                    sort($types);
                    $messageText .= "Тип устройства: ".implode(', ', $types).PHP_EOL;
                }
                if ($model->platforms) {
                    $platforms = ArrayHelper::getColumn($model->platforms, 'title');
                    sort($platforms);
                    $messageText .= "Платформа: ".implode(', ', $platforms).PHP_EOL;
                }
                if ($model->protocols) {
                    $protocols = ArrayHelper::getColumn($model->protocols, 'title');
                    sort($protocols);
                    $messageText .= "Протоколы: ".implode(', ', $protocols).PHP_EOL;
                }
                if ($tagPair) {
                    $messageText .= "Режим спаривания, сброса: ".$tagPair->title.PHP_EOL;
                }
                if ($supportSprutHub) {
                    $messageText .= PHP_EOL.'Поддержка Sprut.Hub ✅';
                }

                $url = 'https://v2.sprut.ai/catalog/item/'.$model->seo->slugify;

                $title = "";
                if ($supportSprutHub) {
                    $title .= "🐙 ";
                }
                $title .= $model->getTitle_vendor_model();
                if ($commentsValue) {
                    $title .= " (💬".$commentsValue.")";
                }

                $urlText = 'Перейти к устройству';
                if ($commentsValue) {
                    $urlText .= " - 💬".$commentsValue;
                }

                $item = [
                    'id' => $model->id,
                    'title' => $title,
                    'description' => $description,
                    'url' => $url,
                    'hide_url' => false,
                    'cache_time' => 0,
                    'parse_mode' => 'HTML',
                    'input_message_content' => new InputTextMessageContent([
                        'message_text' => $messageText,
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => false,
                    ]),
                    'reply_markup' =>[
                        'inline_keyboard' => [
                            [
                                ['text' => $urlText, 'url' => $url],
                            ]
                        ],
                    ],
                ];

                if ($model->image && $model->image->getFileExists()) {
                    $item['thumb_url'] = $model->image->getImageSrc('200', '200', Mode::RESIZE);
                    $item['thumb_width'] = 200;
                    $item['thumb_height'] = 200;
                }

                $results[] = new InlineQueryResultArticle($item);
            }
        }
		
		
		$data['results'] = '['.implode(',', $results).']';
		
		$result = Request::answerInlineQuery($data);
		if (!$result->isOk()) {
			Helpers::dump($result->printError(true));
		}

		return $result;
	}

    public function execute_() {
        $inline_query = $this->getInlineQuery();
        $query = $inline_query->getQuery();

        $data = ['inline_query_id' => $inline_query->getId()];
        $results = [];

        if ($query !== '') {
            $ids = (new Query())->select('id')->from('idx_content')->match($query)->groupBy('id')->column();

            /** @var \common\modules\content\models\Article[] $articles */
            $articles = Article::find()->andWhere(['in', 'id', $ids])->limit(10)->all();

            if ($articles) {
                foreach ($articles as $article) {

                    $text = '<b>'.$article->title.'</b>'.PHP_EOL;
                    $text.= 'Автор: <b>'.$article->getAuthorName().'</b>';
                    if (strlen($article->descr)) {
                        $textLength = mb_strlen($text);
                        $descrLength = 4096 - $textLength;

                        $text .= PHP_EOL.PHP_EOL.StringHelper::truncate($article->descr, $descrLength);
                    }

                    $url = Url::base('https').'/client/article/'.$article->id;
                    $urlInstantView = 'https://t.me/iv?url='.$url.'&rhash=c36fdcf4f21bb7';

                    $messageText = $article->title.'<a href="'.$urlInstantView.'">.</a>';

                    $item = [
                        'id' => $article->id,
                        'title' => $article->title,
                        'description' => $article->descr,
                        'url' => $url,
                        'hide_url' => false,
                        'cache_time' => 60,
                        'parse_mode' => 'HTML',
                        'input_message_content' => new InputTextMessageContent([
                            'message_text' => $messageText,
                            'parse_mode' => 'HTML',
                        ]),
                        'reply_markup' =>[
                            'inline_keyboard' => [
                                [
                                    ['text' => 'Читать статью', 'url' => $url],
                                ]
                            ],
                        ],
                    ];

                    if ($article->image && $article->image->getFileExists()) {
                        $item['thumb_url'] = Url::base('https').$article->image->getImageSrc('100', '100', Mode::CROP_CENTER);
                    }

                    $results[] = new InlineQueryResultArticle($item);
                }
            }
        }


        $data['results'] = '['.implode(',', $results).']';

        $result = Request::answerInlineQuery($data);
        if (!$result->isOk()) {
            Helpers::dump($result->printError(true));
        }


        return $result;
    }


    /**
     * @param $model
     * @param $tagId
     *
     * @return Tag|null
     */
    private function _findTagById($model, $tagId) {
        foreach($model->tags as $tag) {
            foreach ($tag->links as $link) {
                if ($link->id == $tagId) {
                    foreach ($link->links as $ll) {
                        if (in_array($ll->id, $model->tags_ids)) {
                            return $ll;
                        }
                    }
                }
            }
        }
        return null;
    }
	
}