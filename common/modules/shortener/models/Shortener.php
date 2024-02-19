<?php
namespace common\modules\shortener\models;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\shortener\models\query\ShortenerQuery;
use common\modules\user\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%shortener}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $short_url
 * @property string $description
 * @property string $hash
 * @property integer $counter
 * @property integer $countHits
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $expiration_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\shortener\models\ShortenerHit[] $hits
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 * @property \common\modules\user\models\User $owner
 */
class Shortener extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%shortener}}';
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
            [['title', 'url', 'hash'], 'required'],
		    [['status', 'created_by', 'updated_by', 'expiration_at', 'created_at', 'updated_at'], 'integer'],
            [['title', 'description', 'hash'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 1000],
            [['url'], 'url', 'defaultScheme' => 'http', 'skipOnEmpty' => true],
            [['hash'], 'unique'],
            [['hash'], 'validateHash'],
            [['expiration_date'], 'safe'],
		];
	}
    
    /**
     * Generate and validate hash.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateHash($attribute, $params) {
        if (self::findByHash($this->hash)) {
            $this->addError($attribute, 'Не удалось создать короткую ссылку, попробуйте еще раз.');
        }
    }

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('shortener', 'field_id'),
            'title' => Yii::t('shortener', 'field_title'),
			'url' => Yii::t('shortener', 'field_url'),
            'short_url' => Yii::t('shortener', 'field_short_url'),
			'description' => Yii::t('shortener', 'field_description'),
			'hash' => Yii::t('shortener', 'field_hash'),
            'counter' => Yii::t('shortener', 'field_counter'),
			'status' => Yii::t('shortener', 'field_status'),
			'created_by' => Yii::t('shortener', 'field_created_by'),
			'updated_by' => Yii::t('shortener', 'field_updated_by'),
			'expiration_at' => Yii::t('shortener', 'field_expiration_at'),
            'expiration_date' => Yii::t('shortener', 'field_expiration_date'),
			'created_at' => Yii::t('shortener', 'field_created_at'),
			'updated_at' => Yii::t('shortener', 'field_updated_at'),
		];
	}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::SHORTENER;
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\shortener\models\query\ShortenerQuery the active query used by this AR class.
	 */
	public static function find() {
		return new ShortenerQuery(get_called_class());
	}
    
    /**
     * Finds link by hash
     *
     * @param string $hash
     * @return static|null
     */
    public static function findByHash($hash, $allowRoles = []) {
        return static::findOne(['hash' => $hash]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHits() {
        return $this->hasMany(ShortenerHit::class, ['link_id' => 'id']);
    }
    /**
     * @return int
     */
    public function getCountHits() {
        return ShortenerHit::find()->where(['link_id' => $this->id])->count();
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
     * @return \yii\db\ActiveQuery
     */
    public function getOwner() {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
    
    /**
     * Get expiration date
     * @param string $format
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getExpiration_date($format = 'dd-MM-yyyy') {
        if ($this->expiration_at)
		    return Yii::$app->formatter->asDate($this->expiration_at, $format);
        return null;
    }
    
    /**
     * Set expiration date
     * @param $val
     */
    public function setExpiration_date($val) {
        if ($val) {
            $val .= ' 00:00:00';
            $this->expiration_at = strtotime($val);
        }
    }
    
    /**
     * Get created date
     * @param string $format
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getCreated_date($format = 'dd-MM-yyyy') {
        if (!$this->created_at)
            $this->created_at = time();
        return Yii::$app->formatter->asDate($this->created_at, $format);
    }
    
    /**
     * Set created date
     * @param $val
     */
    public function setCreated_date($val) {
        $val .= ' 00:00:00';
        $this->created_at = strtotime($val);
    }
    
    public function getShort_url() {
        $module = Yii::$app->getModule('shortener');
        return $module->hostScheme.'://'.$module->hostName.'/s/'.$this->hash;
    }
    
    /**
     * Generate hash for shorten url
     *
     * @param null $timestamp
     * @return string
     */
    public static function generateHash($timestamp = null) {
        if ($timestamp === null) {
            $timestamp = strtotime("now");
        }
        return base_convert($timestamp, 10, 36);
    }
    /**
     * @param $ip
     * @param $ua
     * @return bool
     */
    public function generateHit($ip = null, $ua = null) {
        $hit = new ShortenerHit();
        $hit->link_id = $this->id;
        $hit->ip = $ip;
        $hit->user_agent = $ua;
        
        return $hit->save();
    }
    
    /**
     * @return bool
     */
    public function isActive() {
        if ($this->expiration_at) {
            return time() < $this->expiration_at;
        }
        return true;
    }
    
    /**
     * Update counter
     */
    public function updateCounter() {
        $this->counter++;
        $this->save(false);
    }
    
    /**
     * @param $insert
     *
     * @return bool
     */
    public function beforeValidate() {
        $this->hash = Shortener::generateHash();
        
        return parent::beforeValidate();
    }
}
