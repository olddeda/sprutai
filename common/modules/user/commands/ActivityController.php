<?php
namespace common\modules\user\commands;

use common\modules\achievement\models\AchievementUser;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\db\ActiveQuery;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\content\models\Content;

use common\modules\comments\models\Comment;

use common\modules\vote\Module as VoteModule;
use common\modules\vote\models\Vote;

use common\modules\user\models\User;
use common\modules\user\models\UserActivity;
use common\modules\user\helpers\enum\ActivityType;

/**
 * Class ActivityController
 * @package common\modules\userc\commands
 */
class ActivityController extends Controller
{
    public function actionIndex() {
        $this->_parseSignup();
        $this->_parseContent();
        $this->_parseCommentContent();
        $this->_parseCommentReview();
        $this->_parseReview();
        $this->_parseLikesContent();
        $this->_parseLikesComment();
        $this->_parseLikedContent();
        $this->_parseLikedComment();
        $this->_parseSubscribes();
        $this->_parseSubscribed();
        $this->_parseAchievement();
    }

    private function _parseSignup() {
        $query = User::find()
            ->joinWith([
                'activities' => function($query) {
                    $query->onCondition('
                        type = '.ActivityType::SIGNUP.'
                    ');
                }
            ])
            ->where('deleted_at IS NULL AND '.UserActivity::tableName().'.id IS NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'SIGNUP ');
        foreach ($query->batch(50) as $users) {

            /** @var User $user */
            foreach ($users as $user) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::SIGNUP;
                $model->module_type = ModuleType::USER;
                $model->module_id = $user->id;
                $model->user_id = $user->id;
                $model->from_user_id = $user->id;
                $model->date_at = $user->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseContent() {
        $query = Content::find()
            ->alias('c')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::CONTENT.' AND ua.user_id = c.author_id AND ua.module_type = '.ModuleType::CONTENT.' AND ua.module_id = c.id')
            ->leftJoin(['u' => User::tableName()], 'u.id = c.author_id')
            ->where([
                'c.status' => Status::ENABLED,
                'ua.id' => null,
            ])->andWhere('u.id IS NOT NULL');
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'CONTENT ');
        foreach ($query->batch(50) as $contents) {

            /** @var Content $content */
            foreach ($contents as $content) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::CONTENT;
                $model->module_type = ModuleType::CONTENT;
                $model->module_id = $content->id;
                $model->user_id = $content->author_id;
                $model->from_user_id = $content->author_id;
                $model->date_at = !is_null($content->date_at) ? $content->date_at : $content->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseCommentContent() {
        $query = Comment::find()
            ->alias('c')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::COMMENT.' AND ua.user_id = c.created_by AND ua.parent_module_type = '.ModuleType::CONTENT.' AND ua.parent_module_id = c.entity_id')
            ->leftJoin(['u' => User::tableName()], 'u.id = c.created_by')
            ->where([
                'c.status' => Status::ENABLED,
                'c.module_type' => ModuleType::CONTENT,
                'ua.id' => null
            ])
            ->andWhere('u.id IS NOT NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'COMMENT CONTENT ');
        foreach ($query->batch(50) as $comments) {

            /** @var Comment $comment */
            foreach ($comments as $comment) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::COMMENT;
                $model->module_type = ModuleType::COMMENT;
                $model->module_id = $comment->id;
                $model->parent_module_type = ModuleType::CONTENT;
                $model->parent_module_id = $comment->entity_id;
                $model->user_id = $comment->created_by;
                $model->from_user_id = $comment->created_by;
                $model->date_at = $comment->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseCommentReview() {
        $query = Comment::find()
            ->alias('c')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::COMMENT.' AND ua.user_id = c.created_by AND ua.parent_module_type = '.ModuleType::CATALOG_ITEM.' AND ua.parent_module_id = c.entity_id')
            ->leftJoin(['u' => User::tableName()], 'u.id = c.created_by')
            ->where([
                'c.status' => Status::ENABLED,
                'c.module_type' => ModuleType::CATALOG_ITEM,
                'ua.id' => null
            ])->andWhere('c.level != 1 AND u.id IS NOT NULL');
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'COMMENT REVIEW ');
        foreach ($query->batch(50) as $comments) {

            /** @var Comment $comment */
            foreach ($comments as $comment) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::COMMENT;
                $model->module_type = ModuleType::COMMENT;
                $model->module_id = $comment->id;
                $model->parent_module_type = ModuleType::CATALOG_ITEM;
                $model->parent_module_id = $comment->entity_id;
                $model->user_id = $comment->created_by;
                $model->from_user_id = $comment->created_by;
                $model->date_at = $comment->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseReview() {
        $query = Comment::find()
            ->alias('c')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::REVIEW.' AND ua.user_id = c.created_by AND ua.parent_module_type = '.ModuleType::CATALOG_ITEM.' AND ua.parent_module_id = c.entity_id')
            ->leftJoin(['u' => User::tableName()], 'u.id = c.created_by')
            ->where([
                'c.status' => Status::ENABLED,
                'c.module_type' => ModuleType::CATALOG_ITEM,
                'ua.id' => null
            ])->andWhere('c.level = 1 AND u.id IS NOT NULL');
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'REVIEW ');
        foreach ($query->batch(50) as $comments) {

            /** @var Comment $comment */
            foreach ($comments as $comment) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::REVIEW;
                $model->module_type = ModuleType::COMMENT;
                $model->module_id = $comment->id;
                $model->parent_module_type = ModuleType::CATALOG_ITEM;
                $model->parent_module_id = $comment->entity_id;
                $model->user_id = $comment->created_by;
                $model->from_user_id = $comment->created_by;
                $model->date_at = $comment->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseLikesContent() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $entity = $module->encodeEntity(Vote::CONTENT_VOTE);

        $query = Vote::find()
            ->alias('v')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::LIKES.' AND ua.user_id = v.user_id AND ua.module_type = '.ModuleType::CONTENT.' AND ua.module_id = v.entity_id')
            ->leftJoin(['u' => User::tableName()], 'u.id = v.user_id')
            ->where([
                'v.value' => 1,
                'v.entity' => $entity,
                'ua.id' => null
            ])
            ->andWhere('u.id IS NOT NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'LIKES CONTENT ');
        foreach ($query->batch(50) as $votes) {

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::LIKES;
                $model->module_type = ModuleType::CONTENT;
                $model->module_id = $vote->entity_id;
                $model->user_id = $vote->user_id;
                $model->from_user_id = $vote->user_id;
                $model->date_at = $vote->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseLikesComment() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $entity = $module->encodeEntity(Vote::COMMENT_VOTE);

        $query = Vote::find()
            ->alias('v')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::LIKES.' AND ua.user_id = v.user_id AND ua.module_type = '.ModuleType::COMMENT.' AND ua.module_id = v.entity_id')
            ->joinWith(['comment'])
            ->leftJoin(['u' => User::tableName()], 'u.id = v.user_id')
            ->where([
                'v.value' => 1,
                'v.entity' => $entity,
                'ua.id' => null,
            ])->andWhere(Comment::tableName().'.id IS NOT NULL AND u.id IS NOT NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'LIKES COMMENT ');
        foreach ($query->batch(50) as $votes) {

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::LIKES;
                $model->module_type = ModuleType::COMMENT;
                $model->module_id = $vote->entity_id;
                $model->parent_module_type = ($vote->comment) ? $vote->comment->module_type : null;
                $model->parent_module_id = ($vote->comment) ? $vote->comment->module_id : null;
                $model->user_id = $vote->user_id;
                $model->from_user_id = $vote->user_id;
                $model->date_at = $vote->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }

    private function _parseLikedContent() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $entity = $module->encodeEntity(Vote::CONTENT_VOTE);

        $query = Vote::find()
            ->alias('v')
            ->leftJoin(['ua' => UserActivity::tableName()], '
                ua.type = '.ActivityType::LIKED.' AND 
                ua.from_user_id = v.user_id AND 
                ua.module_type = '.ModuleType::CONTENT.' AND 
                ua.module_id = v.entity_id
            ')
            ->joinWith(['content'])
            ->leftJoin(['u' => User::tableName()], 'u.id = v.user_id')
            ->leftJoin(['uc' => User::tableName()], 'uc.id = '.Content::tableName().'.author_id')
            ->where([
                'v.value' => 1,
                'v.entity' => $entity,
                'ua.id' => null
            ])
            ->andWhere(Content::tableName().'.id IS NOT NULL AND u.id IS NOT NULL AND uc.id IS NOT NULL')
        ;

        $total = $query->count();

        $i = 0;

        Console::startProgress(0, $total, 'LIKED CONTENT ');
        foreach ($query->batch(50) as $votes) {

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::LIKED;
                $model->module_type = ModuleType::CONTENT;
                $model->module_id = $vote->entity_id;
                $model->user_id = $vote->content->author_id;
                $model->from_user_id = $vote->user_id;
                $model->date_at = $vote->created_at;
                $model->save();
                if (!$model->save()) {
                    var_dump($model->errors);die;
                }
            }
        }
        Console::endProgress();
    }

    private function _parseLikedComment() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $entity = $module->encodeEntity(Vote::COMMENT_VOTE);

        $query = Vote::find()
            ->alias('v')
            ->leftJoin(['ua' => UserActivity::tableName()], '
                ua.type = '.ActivityType::LIKED.' AND 
                ua.from_user_id = v.user_id AND 
                ua.module_type = '.ModuleType::COMMENT.' AND 
                ua.module_id = v.entity_id
            ')
            ->leftJoin(['u' => User::tableName()], 'u.id = v.user_id')
            ->leftJoin(['uc' => User::tableName()], 'uc.id = '.Comment::tableName().'.created_by')
            ->joinWith(['comment'])
            ->where([
                'v.value' => 1,
                'v.entity' => $entity,
                'ua.id' => null
            ])
            ->andWhere(Comment::tableName().'.id IS NOT NULL AND u.id IS NOT NULL AND uc.id IS NOT NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'LIKED COMMENT ');
        foreach ($query->batch(50) as $votes) {

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::LIKED;
                $model->module_type = ModuleType::COMMENT;
                $model->module_id = $vote->entity_id;
                $model->parent_module_type = ($vote->comment) ? $vote->comment->module_type : null;
                $model->parent_module_id = ($vote->comment) ? $vote->comment->module_id : null;
                $model->user_id = $vote->comment->created_by;
                $model->from_user_id = $vote->user_id;
                $model->date_at = $vote->created_at;
                $model->save();
                if (!$model->save()) {
                    var_dump($model->errors);die;
                }
            }
        }
        Console::endProgress();
    }

    private function _parseSubscribes() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $entity = $module->encodeEntity(Vote::USER_FAVORITE);

        $query = Vote::find()
            ->alias('v')
            ->leftJoin(['ua' => UserActivity::tableName()], '
                ua.type = '.ActivityType::SUBSCRIBES.' AND 
                ua.module_id = v.entity_id AND 
                ua.module_type = '.ModuleType::USER.'
            ')
            ->leftJoin(['u' => User::tableName()], 'u.id = v.user_id')
            ->where([
                'v.value' => 1,
                'v.entity' => $entity,
                'ua.id' => null
            ])
            ->andWhere('u.id IS NOT NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'SUBSCRIBES USER ');
        foreach ($query->batch(50) as $votes) {

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::SUBSCRIBES;
                $model->module_type = ModuleType::USER;
                $model->module_id = $vote->entity_id;
                $model->user_id = $vote->user_id;
                $model->from_user_id = $vote->user_id;
                $model->date_at = $vote->created_at;
                $model->save();
                if (!$model->save()) {
                    var_dump($model->errors);die;
                }
            }
        }
        Console::endProgress();
    }

    private function _parseSubscribed() {

        /** @var VoteModule $module */
        $module = Yii::$app->getModule('vote');

        $entity = $module->encodeEntity(Vote::USER_FAVORITE);

        $query = Vote::find()
            ->alias('v')
            ->leftJoin(['ua' => UserActivity::tableName()], '
                ua.type = '.ActivityType::SUBSCRIBED.' AND 
                ua.module_id = v.user_id AND 
                ua.module_type = '.ModuleType::USER.'
            ')
            ->leftJoin(['u' => User::tableName()], 'u.id = v.user_id')
            ->leftJoin(['uc' => User::tableName()], 'uc.id = v.entity_id')
            ->where([
                'v.value' => 1,
                'v.entity' => $entity,
                'ua.id' => null
            ])
            ->andWhere('v.id IS NOT NULL AND u.id IS NOT NULL AND uc.id IS NOT NULL')
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'SUBSCRIBED USER ');
        foreach ($query->batch(50) as $votes) {

            /** @var Vote $vote */
            foreach ($votes as $vote) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::SUBSCRIBED;
                $model->module_type = ModuleType::USER;
                $model->module_id = $vote->user_id;
                $model->user_id = $vote->entity_id;
                $model->from_user_id = $vote->user_id;
                $model->date_at = $vote->created_at;
                $model->save();
                if (!$model->save()) {
                    var_dump($model->errors);die;
                }
            }
        }
        Console::endProgress();
    }

    private function _parseAchievement() {
        $query = AchievementUser::find()
            ->alias('c')
            ->leftJoin(['ua' => UserActivity::tableName()], 'ua.type = '.ActivityType::ACHIEVEMENT.' AND ua.user_id = c.user_id AND ua.module_type = '.ModuleType::ACHIEVEMENT_USER.' AND ua.module_id = c.id')
            ->where([
                'ua.id' => null
            ])
        ;

        $total = $query->count();
        $i = 0;

        Console::startProgress(0, $total, 'ACHIEVEMENT ');
        foreach ($query->batch(50) as $achievementUsers) {

            /** @var AchievementUser $achievementUser */
            foreach ($achievementUsers as $achievementUser) {
                Console::updateProgress(++$i, $total);

                $model = new UserActivity();
                $model->type = ActivityType::ACHIEVEMENT;
                $model->module_type = ModuleType::ACHIEVEMENT_USER;
                $model->module_id = $achievementUser->id;
                $model->parent_module_type = ModuleType::ACHIEVEMENT;
                $model->parent_module_id = $achievementUser->achievement_id;
                $model->user_id = $achievementUser->user_id;
                $model->from_user_id = $achievementUser->user_id;
                $model->date_at = $achievementUser->created_at;
                $model->save();
            }
        }
        Console::endProgress();
    }
}