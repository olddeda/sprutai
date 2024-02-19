<?php
namespace common\modules\paste\models;

use common\modules\base\helpers\Html;
use common\modules\user\helpers\Password;
use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;

use common\modules\paste\models\query\PasteQuery;
use yii\helpers\Url;


/**
 * This is the model class for table "{{%paste}}".
 *
 * @property integer $id
 * @property string $slug
 * @property string $mode
 * @property string $title
 * @property string $descr
 * @property string $code
 * @property boolean $is_private
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class Paste extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%paste}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['mode', 'code', 'status'], 'required'],
			[['created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['descr'], 'string', 'max' => 255],
			[['slug'], 'string', 'max' => 8],
			[['mode'], 'string', 'max' => 100],
			[['code'], 'string', 'max' => 100000],
			[['is_private'], 'boolean'],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
			[['descr', 'code'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
			[['descr', 'code'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('paste', 'field_id'),
			'slug' => Yii::t('paste', 'field_slug'),
			'mode' => Yii::t('paste', 'field_mode'),
			'descr' => Yii::t('paste', 'field_descr'),
			'code' => Yii::t('paste', 'field_code'),
			'is_private' => Yii::t('paste', 'field_is_private'),
			'status' => Yii::t('paste', 'field_status'),
			'created_by' => Yii::t('paste', 'field_created_by'),
			'updated_by' => Yii::t('paste', 'field_updated_by'),
			'created_at' => Yii::t('paste', 'field_created_at'),
			'updated_at' => Yii::t('paste', 'field_updated_at'),
		];
	}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::PASTE;
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\content\models\query\ContentQuery the active query used by this AR class.
	 */
	public static function find() {
		return new PasteQuery(get_called_class());
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
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
	 * @return string
	 */
	public function getTitle() {
		return '№'.$this->id;
	}
	
	/**
	 * @return string
	 */
	public function getLink($url = 'paste') {
		return Url::to('/client/'.$url.'/'.$this->slug, true);
	}
	
	/**
	 * @return bool
	 */
	public function getIsOwn() {
		if (Yii::$app->user->getIsAdmin())
			return true;
		return $this->created_by == Yii::$app->user->id;
	}
	
	public function afterFind() {
		parent::afterFind();
		
		$this->code = Html::decode($this->code);
	}
	
	/**
	 * @param $insert
	 *
	 * @return bool
	 */
	public function beforeSave($insert) {
		if ($insert) {
			$this->slug = Password::generate(8);
			if (self::find()->where(['slug' => $this->slug])->exists())
				return $this->beforeSave($insert);
		}
		return parent::beforeSave($insert);
	}
}
