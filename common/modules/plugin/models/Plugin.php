<?php
namespace common\modules\plugin\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\content\helpers\enum\Type;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;

use common\modules\payment\models\Payment;
use common\modules\payment\helpers\enum\Status as StatusPayment;

use common\modules\content\models\Content;
use common\modules\content\models\Instruction;

/**
 * Defined relations:
 * @property \common\modules\plugin\models\Version $version
 * @property \common\modules\plugin\models\Version[] $versions
 * @property \common\modules\content\models\Instruction $instruction
 * @property \common\modules\payment\models\Payment $payment
 */

class Plugin extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::PLUGIN;
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
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'background',
				'type' => MediaType::IMAGE,
			],
			[
				'class' => MediaBehavior::class,
				'attribute' => 'logo',
				'type' => MediaType::IMAGE,
			],
		]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getInstruction() {
		return $this->hasOne(Instruction::class, ['content_id' => 'id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getVersions() {
		return $this->hasMany(Version::class, ['plugin_id' => 'id'])->orderBy(["INET_ATON(SUBSTRING_INDEX(CONCAT(version,'.0.0.0'),'.',4))" => SORT_DESC]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getVersion() {
		return $this->hasOne(Version::class, ['plugin_id' => 'id'])->andWhere(['latest' => true]);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPayment() {
		return $this->hasOne(Payment::class, ['module_id' => 'id'])->andWhere(['module_type' => self::moduleType(), 'status' => StatusPayment::PAID, 'user_id' => Yii::$app->user->id]);
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'plugins';
	}
	
	/**
	 * Is free plugin
	 * @return mixed
	 */
	public function getIsFree() {
		$ptms = $this->paymentTypeModule;
		
		if (is_array($ptms) && count($ptms))
			return $ptms[0]->price_free;
		return false;
	}
	
	/**
	 * Is paid plugin
	 * @return bool
	 */
	public function getIsPaid() {
		if ($this->getIsFree())
			return true;
		return ($this->payment) ? $this->payment->status == StatusPayment::PAID : false;
	}
	
	/**
	 * Check can download
	 * @return bool
	 */
	public function getCanDownload() {
		if (!$this->version || ($this->version && $this->version->status != Status::ENABLED))
			return false;
		if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor())
			return true;
		return $this->getIsPaid();
	}
	
	/**
	 * Get download url
	 * @return string
	 */
	public function getDownloadUrl() {
		return $this->version->getDownloadUrl();
	}
	
	/**
	 * Is can paid plugin
	 * @return bool
	 */
	public function getCanPaid() {
		if ($this->getIsFree())
			return true;
		return ($this->payment && $this->payment->status == StatusPayment::PAID) ? false : true;
	}
}