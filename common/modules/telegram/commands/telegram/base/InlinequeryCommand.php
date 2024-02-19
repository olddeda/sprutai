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

use common\modules\content\models\Article;
use common\modules\media\helpers\enum\Mode;
use common\modules\telegram\helpers\Helpers;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
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
        return Request::emptyResponse();

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