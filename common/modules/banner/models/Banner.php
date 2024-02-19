<?php
namespace common\modules\banner\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;

use common\modules\user\models\User;

use common\modules\banner\models\query\BannerQuery;


/**
 * This is the model class for table "{{%banner}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property integer $views
 * @property integer $visits
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $date_from_at
 * @property integer $date_to_at
 * @property integer $created_at
 * @property integer $updated_at
 */
class Banner extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%banner}}';
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'image',
				'type' => MediaType::IMAGE,
			],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['title', 'url', 'status', 'date_from', 'date_to'], 'required'],
			[['views', 'visits', 'status', 'created_by', 'updated_by', 'created_at', 'date_from_at', 'date_to_at', 'updated_at'], 'integer'],
			[['title', 'url'], 'string', 'max' => 255],
			[['url'], 'url', 'defaultScheme' => 'http'],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
			[['date_from', 'date_to'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('banner', 'field_id'),
			'title' => Yii::t('banner', 'field_title'),
			'url' => Yii::t('banner', 'field_url'),
			'views' => Yii::t('banner', 'field_views'),
			'visits' => Yii::t('banner', 'field_visits'),
			'status' => Yii::t('banner', 'field_status'),
			'created_by' => Yii::t('banner', 'field_created_by'),
			'updated_by' => Yii::t('banner', 'field_updated_by'),
			'date_from' => Yii::t('banner', 'field_date_from'),
			'date_to' => Yii::t('banner', 'field_date_to'),
			'date_from_at' => Yii::t('banner', 'field_date_from_at'),
			'date_to_at' => Yii::t('banner', 'field_date_to_at'),
			'created_at' => Yii::t('banner', 'field_created_at'),
			'updated_at' => Yii::t('banner', 'field_updated_at'),
		];
	}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::BANNER;
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\content\models\query\ContentQuery the active query used by this AR class.
	 */
	public static function find() {
		return new BannerQuery(get_called_class());
	}
	
	/**
	 * @return \common\modules\content\models\query\ContentQuery
	 */
	public static function findActive() {
		return self::find()
			->andFilterWhere(['<=', 'date_from_at', time()])
			->andFilterWhere(['>=', 'date_to_at', time()])
			->andWhere(['status' => Status::ENABLED]);
	}
	
	/**
	 * Count active banners
	 * @return int|string
	 */
	public static function countActive() {
		return self::findActive()->count();
	}
	
	/**
	 * Get one active banner
	 * @return array|null|\yii\db\ActiveRecord
	 */
	public static function getActive() {
		return self::findActive()->one();
	}
	
	/**
	 * Get all active banner
	 * @return array|null|\yii\db\ActiveRecord
	 */
	public static function getActives() {
		return self::findActive()->orderBy(['created_at' => SORT_DESC])->all();
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}
	
	/**
	 * Get date from formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
	public function getDate_from($format = 'dd-MM-yyyy') {
		if (!$this->date_from_at)
			$this->date_from_at = time();
		return Yii::$app->formatter->asDate($this->date_from_at, $format);
	}
	
	/**
	 * Get date to formatted
	 * @param string $format
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getDate_to($format = 'dd-MM-yyyy') {
		if (!$this->date_to_at)
			$this->date_to_at = time() + 86400;
		return Yii::$app->formatter->asDate($this->date_to_at, $format);
	}
	
	/**
	 * Set date from
	 * @param $val
	 */
	public function setDate_from($val) {
		$val .= ' 00:00:00';
		$this->date_from_at = strtotime($val);
	}
	
	/**
	 * Set date to
	 * @param $val
	 */
	public function setDate_to($val) {
		$val .= ' 23:59:59';
		$this->date_to_at = strtotime($val);
	}
	
	public function getDateBeginDay() {
		return \DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->setTimestamp($this->date_from_at)->format('Y-m-d 00:00:00'))->getTimestamp();
	}
	
	public function getDateEndDay() {
		return \DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->setTimestamp($this->date_to_at)->format('Y-m-d 23:59:59'))->getTimestamp();
	}
	
	/**
	 * Update views
	 *
	 * @return bool
	 */
	public function updateViews() {
		return $this->updateCounters(['views' => 1]);
	}
	
	/**
	 * Update visits
	 *
	 * @return bool
	 */
	public function updateVisits() {
		return $this->updateCounters(['visits' => 1]);
	}
}
