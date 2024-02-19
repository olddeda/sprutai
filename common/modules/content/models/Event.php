<?php
namespace common\modules\content\models;

use common\modules\project\models\Project;
use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Debug;
use common\modules\base\helpers\Url;
use common\modules\base\extensions\sitemap\SitemapInterface;

use common\modules\tag\models\Tag;

use common\modules\vote\models\Vote;

use common\modules\user\models\User;

use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type;

use common\modules\social\models\SocialItem;

class Event extends Content implements SitemapInterface
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::EVENT;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[['descr'], 'required'],
		]);
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'event';
	}
	
	
	/**
	 * @return query\ContentQuery
	 */
	public static function sitemap() {
		return self::find()->andWhere(['status' => Status::ENABLED]);
	}
	
	/**
	 * @return string
	 */
	public function getSitemapUrl() {
		return \yii\helpers\Url::toRoute(['event/view', 'id' => $this->id], true);
	}
	
	/**
	 * Send to subsribers author about new post
	 *
	 * @param $changedAttributes
	 *
	 * @throws \ReflectionException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function eventAuthorSubscribersPublication($changedAttributes) {
		if (!Yii::$app->settings->get('notification', 'event_subscribers'))
			return;
		
		$oldPublishedAt = (isset($changedAttributes['published_at']) && $changedAttributes['published_at']) ? $changedAttributes['published_at'] : null;
		
		
		$subscribersAuthorIds = User::find()->select(User::tableName().'.id')->subscribers(Vote::USER_FAVORITE, $this->author_id)->column();
		$subscribersProject = User::find()->select(User::tableName().'.id')->subscribers(Vote::CONTENT_FAVORITE, $this->module_id)->column();
		$subscribersIds = array_unique(array_merge($subscribersAuthorIds, $subscribersProject));
		
		if (count($subscribersIds)) {
			$project = Project::find()->where(['id' => $this->module_id])->one();
			
			$subject = Yii::t('notification', 'event_subscribe_create_subject', [
				'author' => $this->getAuthorName(true),
			]);
			$message = Yii::t('notification', 'event_subscribe_create', [
				'title' => $this->title,
				'url' => Url::to(['/projects/event-view', 'project_id' => $project->id, 'id' => $this->id], true),
				'author' => $this->getAuthorName(true),
				'author_url' => Url::toRoute(['/user/profile/view', 'id' => $this->author_id], true),
				'project_title' => $project->title,
				'project_url' => Url::to(['/projects/view', 'id' => $project->id],true),
			]);
			
			Yii::$app->notification->queue($subscribersIds, $subject, $message, 'author');
		}
	}
	
	/**
	 * Send to author about return to draft
	 *
	 * @param $changedAttributes
	 */
	public function eventAuthorDraft($changedAttributes) {
		$subject = Yii::t('notification', 'event_draft_moderate_subject');
		$message = Yii::t('notification', 'event_draft_moderate', [
			'url' => Url::base('https').'/project/event/update/'.$this->module_id.'/'.$this->id,
			'title' => $this->title,
		]);
		
		Yii::$app->notification->queue([$this->author_id], $subject, $message, 'system');
	}
	
	/**
	 * Send to moderate chat
	 *
	 * @param $changedAttributes
	 */
	public function eventModerators($changedAttributes) {
		$subject = Yii::t('notification', 'event_need_moderate_subject');
		$message = Yii::t('notification', 'event_need_moderate', [
			'url' => Url::base('https').'/project/event/update/'.$this->module_id.'/'.$this->id,
			'title' => $this->title,
			'fio' => $this->author->getAuthorName(true)
		]);
		
		Yii::$app->notification->queueTelegramIds(Yii::$app->getModule('telegram')->moderateIds, $message);
	}
}