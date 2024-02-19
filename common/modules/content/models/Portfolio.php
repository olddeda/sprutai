<?php
namespace common\modules\content\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Debug;
use common\modules\base\helpers\Url;
use common\modules\base\extensions\sitemap\SitemapInterface;

use common\modules\tag\models\Tag;

use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type;

class Portfolio extends Content implements SitemapInterface
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::PORTFOLIO;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		$rules = parent::rules();
		unset($rules['required']);
		
		$rules =  ArrayHelper::merge($rules, [
			[['title', 'descr', 'status'], 'required', 'when' => function ($data) {
				return in_array($data->status, [Status::ENABLED, Status::MODERATED]);
			}, 'whenClient' => "function (attribute, value) {
        		return ($('#portfolio-status').val() == 1 || $('#portfolio-status').val() == 4);
    		}"],
		]);
		
		return $rules;
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'porfolio';
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
		if ($this->company_id)
			return \yii\helpers\Url::toRoute(['companies/portfolio/view', 'company_id' => $this->company_id, 'id' => $this->id], true);
		return ni;
	}
	
	/**
	 * Send to moderate chat
	 *
	 * @param $changedAttributes
	 */
	public function eventModerators($changedAttributes) {
		$subject = Yii::t('notification-company', 'portfolio_need_moderate_subject');
		$message = Yii::t('notification-company', 'portfolio_need_moderate', [
			'url' => \yii\helpers\Url::base('https').'/company/portfolio/'.$this->company_id.'/update/'.$this->id,
			'title' => $this->title,
			'company' => $this->owner->title
		]);
		Yii::$app->notification->queueTelegramIds(Yii::$app->getModule('telegram')->moderateIds, $message);
	}
	
}