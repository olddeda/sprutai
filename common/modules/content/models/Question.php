<?php
namespace common\modules\content\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\Url;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\extensions\sitemap\SitemapInterface;
use common\modules\base\components\Debug;

use common\modules\tag\models\Tag;

use common\modules\project\models\Project;

use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type;


class Question extends Content implements SitemapInterface
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::QUESTION;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		$rules = parent::rules();
		unset($rules['required']);
		
		return ArrayHelper::merge($rules, [
			[['title', 'text'], 'required'],
		]);
	}

	public function attributeLabels() {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'title' => Yii::t('content-question', 'field_title'),
            'text' => Yii::t('content-question', 'field_text'),
        ]);
    }

    /**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'question';
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
		return \yii\helpers\Url::toRoute(['question/view', 'id' => $this->id], true);
	}
	
	/**
	 * Send to subscribers author about new post
	 *
	 * @param $changedAttributes
	 *
	 * @throws \ReflectionException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function eventAuthorSubscribersPublication($changedAttributes) {
		if (!$this->company_id && $this->module_type == ModuleType::CONTENT_PROJECT) {
			$project = Project::find()->where(['id' => $this->module_id])->one();
			if ($project) {
				
				$subject = Yii::t('notification', 'question_new_subject', [
					'author' => $this->getAuthorName(true),
				]);
				
				$message = Yii::t('notification', 'question_new', [
					'title' => $this->title,
					'url' => Url::to(['/projects/question/view', 'project_id' => $project->id, 'id' => $this->id], true),
					'user' => $this->getAuthorName(true),
					'user_url' => Url::toRoute(['/user/profile/view', 'id' => $this->author_id], true),
				]);
				
				Yii::$app->notification->queue([$project->author_id], $subject, $message, 'author');
			}
		}
	}
	
}