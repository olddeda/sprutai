<?php
namespace common\modules\shortener\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use UAParser\Parser;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\shortener\models\query\ShortenerHitQuery;

/**
 * This is the model class for table "{{%shortener_link}}".
 *
 * @property integer $id
 * @property integer $link_id
 * @property string $ip
 * @property string $country
 * @property string $city
 * @property string $user_agent
 * @property string $os
 * @property string $os_version
 * @property string $browser
 * @property string $browser_version
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $date_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\shortener\models\Shortener $link
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class ShortenerHit extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%shortener_hit}}';
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
            [['link_id', 'ip', 'user_agent'], 'required'],
            [['link_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['country', 'city', 'os', 'os_version', 'browser', 'browser_version'], 'string'],
            [['ip'], 'string', 'max' => 50],
            [['user_agent'], 'string', 'max' => 255],
            [['link_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shortener::class, 'targetAttribute' => ['link_id' => 'id']],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('shortener_hit', 'field_id'),
			'link_id' => Yii::t('shortener_hit', 'field_link_id'),
            'ip' => Yii::t('shortener_hit', 'field_ip'),
			'country' => Yii::t('shortener_hit', 'field_country'),
			'city' => Yii::t('shortener_hit', 'field_city'),
            'user_agent' => Yii::t('shortener_hit', 'field_user_agent'),
			'os' => Yii::t('shortener_hit', 'field_os'),
            'os_version' => Yii::t('shortener_hit', 'field_os_version'),
            'browser' => Yii::t('shortener_hit', 'field_browser'),
            'browser_version' => Yii::t('shortener_hit', 'field_browser_version'),
			'created_by' => Yii::t('shortener_hit', 'field_created_by'),
			'updated_by' => Yii::t('shortener_hit', 'field_updated_by'),
			'created_at' => Yii::t('shortener_hit', 'field_created_at'),
			'updated_at' => Yii::t('shortener_hit', 'field_updated_at'),
		];
	}

	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::SHORTENER_HIT;
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\shortener\models\query\ShortenerHitQuery the active query used by this AR class.
	 */
	public static function find() {
		return new ShortenerHitQuery(get_called_class());
	}
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLink() {
        return $this->hasOne(Shortener::class, ['id' => 'link_id']);
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
     * Get created date
     * @param string $format
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getCreated_date($format = 'dd-MM-yyyy HH:mm:ss') {
        if (!$this->created_at)
            $this->created_at = time();
        return Yii::$app->formatter->asDateTime($this->created_at, $format);
    }
    
    /**
     * Set created date
     * @param $val
     */
    public function setCreated_date($val) {
        //$val .= ' 00:00:00';
        //$this->created_at = strtotime($val);
    }
    
    /**
     * @param $insert
     *
     * @return bool
     */
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            try {
                $ip = Yii::$app->geoip->ip($this->ip);
                if ($ip->country) {
                    $this->country = $ip->country;
                }
                if ($ip->city) {
                    $this->city = $ip->city;
                }
            } catch (\Exception $e) {}
            try {
                $parser = Parser::create();
                $ua = $parser->parse($this->user_agent);
                $this->os = $ua->os->family;;
                $this->os_version = $ua->os->major;
                $this->browser = $ua->ua->family;
                $this->browser_version = $ua->ua->major;
            } catch (\Exception $e) {}
        }
        return parent::beforeSave($insert);
    }
}
