<?php
namespace client\controllers;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\components\youtube\Youtube;
use common\modules\catalog\models\CatalogItem;
use common\modules\content\models\Article;
use common\modules\media\helpers\enum\Mode;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\sphinx\Connection;
use yii\sphinx\Query;


/**
 * Site controller
 */
class TestController extends Controller
{
	public function behaviors() {
		return [];
	}
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}
	
	public function actionIndex() {
		return $this->render('index');
	
	}

	public function actionTest($query) {
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
                    'stat',
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
                ])
                ->andWhere(['in', CatalogItem::tableName().'.id', $ids])
                ->groupBy(CatalogItem::tableName().'.id')
                ->limit(10)
                ->votes()
                ->all()
            ;

            /** @var CatalogItem $model */
            foreach ($models as $model) {
                $tagPair = null;
               // Debug::dump($model->tags_ids);
                foreach($model->tags as $tag) {
                    foreach ($tag->links as $link) {
                        if ($link->id == 771) {
                            foreach ($link->links as $ll) {
                                if (in_array($ll->id, $model->tags_ids)) {
                                    $tagPair = $ll;
                                    break;
                                }
                            }
                        }
                    }
                }

                $rating = str_repeat('⭐', $model->stat->rating);

                $supportSprutHub = in_array(94, $model->tags_ids);

                $description = $rating.PHP_EOL.PHP_EOL;
                if ($model->types) {
                    $types = ArrayHelper::getColumn($model->types, 'title');
                    sort($types);
                    $description .= "<b>Тип устройства:</b> ".implode(', ', $types).PHP_EOL;
                }
                if ($model->protocols) {
                    $protocols = ArrayHelper::getColumn($model->protocols, 'title');
                    sort($protocols);
                    $description .= "<b>Протоколы:</b> ".implode(', ', $protocols).PHP_EOL;
                }
                if ($supportSprutHub) {
                    $description .= PHP_EOL.'Поддержка Sprut.Hub ✅';
                }

                $messageText = "";
                if ($model->types) {
                    $types = ArrayHelper::getColumn($model->types, 'title');
                    sort($types);
                    $messageText .= "<b>Тип устройства:</b> ".implode(', ', $types).PHP_EOL;
                }
                if ($model->platforms) {
                    $platforms = ArrayHelper::getColumn($model->platforms, 'title');
                    sort($platforms);
                    $messageText .= "<b>Платформа:</b> ".implode(', ', $platforms).PHP_EOL;
                }
                if ($model->protocols) {
                    $protocols = ArrayHelper::getColumn($model->protocols, 'title');
                    sort($protocols);
                    $messageText .= "<b>Протоколы:</b> ".implode(', ', $protocols).PHP_EOL;
                }
                if ($tagPair) {
                    $messageText .= "<b>Режим спаривания, сброса:</b> ".$tagPair->title.PHP_EOL;
                }
                if ($supportSprutHub) {
                    $messageText .= PHP_EOL.'Поддержка Sprut.Hub ✅';
                }

                $url = 'https://v2.sprut.ai/catalog/item/'.$model->seo->slugify;

                $item = [
                    'id' => $model->id,
                    'title' => $model->getTitle_vendor_model(),
                    'description' => $description,
                    'url' => $url,
                    'hide_url' => false,
                    'cache_time' => 0,
                    'parse_mode' => 'HTML',
                    'input_message_content' => new InputTextMessageContent([
                        'message_text' => $messageText,
                        'parse_mode' => 'HTML',
                    ]),
                    'reply_markup' =>[
                        'inline_keyboard' => [
                            [
                                ['text' => 'Перейти к устройству', 'url' => $url],
                            ]
                        ],
                    ],
                ];

                if ($model->image && $model->image->getFileExists()) {
                    $item['thumb_url'] = $model->image->getImageSrc('100', '100', Mode::RESIZE);
                }

                $results[] = new InlineQueryResultArticle($item);
            }
        }

        Debug::dump($results);
    }

    public function actionYoutube() {

	    /** @var Youtube $youtube */
	    $youtube = Yii::$app->youtube;

        $result = $youtube->getVideoInfoUrl('https://www.youtube.com/watch?v=sAoc6vv3Fys&t=12s');

        Debug::dump($result);
    }

    public function actionSphinx() {

	    /** @var Connection $sphinx */
	    $sphinx = Yii::$app->sphinx;

	    $q = 'Xiaomi Roborock S50 Sweep one: 6 аргументов ЗА';
        //$q = 'Xiaomi Roborock S50 Sweep one';
	    //$query = implode(' MAYBE ', explode(' ', $query));

	    //echo $query;

        $query2 = (new Query())
            ->select('*, weight()')
            ->from('idx_catalog_item')
            //->match(Yii::$app->sphinx->escapeMatchValue($query))
                //->match('"'.$query.'"')
            ->match(new Expression(':match', [
                'match' => $q.'\1'
            ]))
            ->options([
                'ranker' => 'wordcount'
            ]);
            //->groupBy('id')
            //->limit(100)

        $query = $sphinx->createCommand('
            select *, weight()
            from idx_catalog_item 
            WHERE MATCH(\'"'.Yii::$app->sphinx->escapeMatchValue($q).'"/3\')
            OPTION ranker=expr(\'sum(lcs*user_weight)\')
        ');

        Debug::dump($query->queryAll());

        //Debug::dump($query->createCommand()->rawSql);

        //Debug::dump($query->all());
        die;

    }

	public function actionEpn() {

	    /** @var \common\modules\base\components\epn\EpnCabinet $epn */
	    $epn = Yii::$app->epnCabinet;
	    $epn->addRequestGetStatisticsByDay('day', 0,  '');
        //$epn->addRequestCheckLink('hour', 'https://google.com');
	    $epn->runRequests();

	    $result = $epn->getRequestResult('day');

        $moneyHold = array_sum(array_map(function($item) {
            return $item['leads_hold'];
        }, $result['data']));

        Debug::dump($moneyHold);

	    die;
    }
}
