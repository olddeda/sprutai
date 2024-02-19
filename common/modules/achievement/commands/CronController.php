<?php
namespace common\modules\achievement\commands;


use common\modules\catalog\models\CatalogItem;
use common\modules\telegram\models\TelegramChat;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\achievement\helpers\enum\Type;
use common\modules\achievement\models\Achievement;
use common\modules\achievement\models\AchievementUser;
use common\modules\achievement\models\AchievementUserStat;

use common\modules\comments\models\Comment;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\helpers\enum\Status as ContentStatus;

use common\modules\favorite\models\Favorite;

use common\modules\vote\Module as VoteModule;
use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;

use common\modules\user\models\User;

/**
 * Class CronController
 * @package common\modules\achievement\commands
 */

class CronController extends Controller
{
    public function actionTest() {
        $catalogItem = CatalogItem::find()->where('id = 1')->one();
        $chatIds = ArrayHelper::merge([-1001082506583], TelegramChat::getIdentifiersContent($catalogItem->tags_ids));
        $chatIds = array_unique($chatIds);
        $chatIds = array_filter($chatIds, function ($id) {
            return $id != -1001437904573;
        });
        //if (isset($chatIds["-1001437904573"]))
        //    unset($chatIds["-1001437904573"]);
        var_dump($chatIds);
        die;


        $message = '<b>Пользователь Олег Челбаев (@sprut666666) получил новое достижение Галактический Многощупалец за активность на портале</b>';

        /** @var Notification $notification */
        $notification = Yii::$app->get('notification');

        $chatId = -1001082506583; // General

        $notification->queueTelegramIds([$chatId], $message, [
            'bot' => 'telegramAchievement',
        ]);

    }

	public function actionRun() {
        $this->addOwners();
        $this->addReviews();
        $this->addComments();
        $this->addContents(Type::ARTICLE, ContentType::ARTICLE);
        $this->addContents(Type::NEWS, ContentType::NEWS);
        $this->addContents(Type::BLOG, ContentType::BLOG);
        $this->addContents(Type::PLUGIN, ContentType::PLUGIN);
        $this->addLikes();
        $this->addLiked();
        $this->addSubscribes();
	}

    private function addOwners() {
        $query = Favorite::find()
            ->where([
                'module_type' => ModuleType::CATALOG_ITEM,
                'group_id' => 4,
            ])
            ->groupBy('user_id')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'OWNERS ');
        foreach ($query->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);
                $userId = $item->user_id;

                $count = Favorite::find()
                    ->select('COUNT(*) AS count')
                    ->where([
                        'module_type' => ModuleType::CATALOG_ITEM,
                        'group_id' => 4,
                        'user_id' => $item->user_id,
                    ])
                    ->scalar();
                ;
                if (!$count) {
                    continue;
                }

                $this->fillLevels(Type::OWNER, $userId, $count);
            }
        }
        Console::endProgress();
    }

    private function addReviews() {
	    $where = [
            'AND',
            [
                'module_type' => ModuleType::CATALOG_ITEM,
                'status' => 1,
            ],
            'parent_id = 0 OR parent_id IS NULL'
        ];

        $query = Comment::find()->where($where);

        $total = $query->groupBy('created_by')->count();
        $i = 0;

        Console::startProgress(0, $total, 'REVIEWS ');

        foreach ($query->groupBy('created_by')->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);

                $userId = $item->created_by;

                $count = Comment::find()
                    ->select('COUNT(*) AS count')
                    ->where($where)
                    ->andWhere(['created_by' => $userId])
                    ->scalar();
                ;
                if (!$count) {
                    continue;
                }

                $this->fillLevels(Type::REVIEW, $userId, $count);
            }
        }
        Console::endProgress();
    }

    private function addComments() {
	    $where = [
            'AND',
            [
                'status' => 1,
            ],
            '(module_type = 81 AND parent_id > 0) OR (module_type != 81)'
        ];

	    $query = Comment::find()
            ->where($where)
        ;

        $total = $query->groupBy('created_by')->count();
        $i = 0;

        Console::startProgress(0, $total, 'COMMENTS ');
        foreach ($query->groupBy('created_by')->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);

                $userId = $item->created_by;
                $count = Comment::find()
                    ->select('COUNT(*) AS count')
                    ->where($where)
                    ->andWhere(['created_by' => $userId])
                    ->scalar();
                ;
                if (!$count) {
                    continue;
                }

                $this->fillLevels(Type::COMMENT, $userId, $count);
            }
        }
        Console::endProgress();
    }

    private function addContents(int $type, int $contentType) {
	    $query = Content::find()
            ->where([
                'type' => $contentType,
                'status' => ContentStatus::ENABLED,
            ])
            ->groupBy('author_id')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, mb_strtoupper(str_replace('type_', '', ContentType::getItem($contentType))).' ');
        foreach ($query->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);

                $userId = $item->author_id;

                $count = Content::find()
                    ->select('COUNT(*) AS count')
                    ->where([
                        'type' => $contentType,
                        'status' => ContentStatus::ENABLED,
                        'author_id' => $userId,
                    ])
                    ->scalar();
                ;
                if (!$count) {
                    continue;
                }

                $this->fillLevels($type, $userId, $count);
            }
        }
        Console::endProgress();
    }

    private function addLikes() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $types = [
            Vote::CONTENT_VOTE,
            Vote::COMMENT_VOTE,
            Vote::CONTEST_VOTE,
            Vote::CATALOG_ITEM_VOTE,
        ];

        $entities = [];
        foreach ($types as $type) {
            $entities[] = $module->encodeEntity($type);
        }

        $query = Vote::find()
            ->where([
                'value' => 1,
                'entity' => $entities,
            ])
            ->groupBy('user_id')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'LIKES ');
        foreach ($query->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);

                $userId = $item->user_id;

                $count = Vote::find()
                    ->select('COUNT(*) AS count')
                    ->where([
                        'value' => 1,
                        'user_id' => $userId,
                    ])
                    ->scalar();
                ;
                if (!$count) {
                    continue;
                }

                $this->fillLevels(Type::LIKES, $userId, $count);
            }
        }
        Console::endProgress();
    }

    private function addLiked() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $types = [
            Vote::CONTENT_VOTE,
            Vote::COMMENT_VOTE,
            Vote::CONTEST_VOTE
        ];

        $entities = [];
        foreach ($types as $type) {
            $entities[] = $module->encodeEntity($type);
        }

        /** @var Query $query */
        $query = (new Query())
            ->select('
                SUM(v.positive) AS count,
                (
                    CASE 
                        WHEN entity = '.$module->encodeEntity(Vote::CONTENT_VOTE).' THEN (
                            SELECT c.author_id
                            FROM '.Content::tableName().' AS c
                            WHERE c.id = v.entity_id
                        )
                        WHEN entity = '.$module->encodeEntity(Vote::COMMENT_VOTE).' THEN (
                            SELECT c.created_by
                            FROM '.Comment::tableName().' AS c
                            WHERE c.id = v.entity_id
                        )
                        WHEN entity = '.$module->encodeEntity(Vote::CONTEST_VOTE).' THEN (
                            SELECT c.author_id
                            FROM '.Content::tableName().' AS c
                            WHERE c.id = v.entity_id
                        )
                        ELSE NULL
                    END      
                ) AS user_id
            ')
            ->from(['v' => VoteAggregate::tableName()])
            ->where([
                'v.entity' => $entities,
            ])
            ->having('user_id IS NOT NULL AND count > 0')
            ->groupBy('user_id')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'LIKED ');
        foreach ($query->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);

                $count = $item['count'];
                $userId = $item['user_id'];

                $this->fillLevels(Type::LIKED, $userId, $count);
            }
        }
        Console::endProgress();
    }

    private function addSubscribes() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $types = [
            Vote::USER_FAVORITE,
        ];

        $entities = [];
        foreach ($types as $type) {
            $entities[] = $module->encodeEntity($type);
        }

        /** @var Query $query */
        $query = (new Query())
            ->select([
                'positive AS count',
                'entity_id AS user_id'
            ])
            ->from(VoteAggregate::tableName())
            ->where([
                'entity' => $entities,
            ])
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'SUBSCRIBED ');
        foreach ($query->batch(50) as $items) {
            foreach ($items as $item) {
                Console::updateProgress(++$i, $total);

                $userId = $item['user_id'];
                $count = $item['count'];

                if (!$count) {
                    continue;
                }

                $this->fillLevels(Type::SUBSCRIBED, $userId, $count);
            }
        }
        Console::endProgress();
    }

    /**
     * @param int $type
     * @param int $userId
     * @param int $count
     */
    private function fillLevels(int $type, int $userId, int $count) {
        if (!User::find()->where(['id' => $userId])->exists()) {
            return;
        };

        $achievements = Achievement::find()->andWhere(['type' => $type])->orderBy('level')->all();

        /** @var Achievement $achievement */
        foreach ($achievements as $achievement) {
            if ($achievement->level <= $count) {
                if (AchievementUser::find()->where([
                    'achievement_id' => $achievement->id,
                    'user_id' => $userId,
                ])->exists()) {
                    continue;
                }

                $achievementUser = new AchievementUser();
                $achievementUser->achievement_id = $achievement->id;
                $achievementUser->user_id = $userId;
                if (!$achievementUser->save()) {
                    var_dump($achievementUser->errors);
                    die;
                }
            }
        }

        $achievementUserStat = AchievementUserStat::find()->where([
            'type' => $type,
            'user_id' => $userId,
        ])->one();
        if (is_null($achievementUserStat)) {
            $achievementUserStat = new AchievementUserStat();
            $achievementUserStat->type = $type;
            $achievementUserStat->user_id = $userId;
        }
        $achievementUserStat->count = $count;
        $achievementUserStat->save();
    }
}