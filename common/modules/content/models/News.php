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


class News extends Content implements SitemapInterface
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::NEWS;
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
		return 'news';
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
		return \yii\helpers\Url::toRoute(['news/view', 'id' => $this->id], true);
	}
	
	/**
	 * List latest blogs
	 * @param int $limit
	 *
	 * @return array|\yii\db\ActiveRecord[]
	 */
	static public function listLatest($limit = 10) {
		return  News::find()->where([News::tableName().'.status' => Status::ENABLED])->limit($limit)->orderBy('created_at DESC')->all();
	}
}