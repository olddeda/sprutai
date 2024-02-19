<?php
namespace common\modules\payment\models;

use Yii;
use yii\helpers\Url;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\components\Debug;

use common\modules\user\models\User;
use common\modules\user\Module as ModuleUser;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Type as ContentType;

use common\modules\project\models\Project;

use common\modules\notification\Module as ModuleNotification;

use common\modules\telegram\models\TelegramChat;

use common\modules\catalog\helpers\enum\StatusOrder;

// TODO: Переделать на \common\modules\catalog\models\CatalogItemOrder
use api\models\catalog\CatalogItemOrder;

use common\modules\payment\models\query\PaymentQuery;
use common\modules\payment\helpers\enum\Status;
use common\modules\payment\helpers\enum\StatusWithdrawal;

/**
 * This is the model class for table "{{%payment}}".
 *
 * @property int $id
 * @property int $kind
 * @property int $module_type
 * @property int $module_id
 * @property int $payment_type_id
 * @property int $user_id
 * @property int $to_user_id
 * @property int $provider_id
 * @property string $provider_error
 * @property double $price
 * @property double $tax
 * @property string $descr
 * @property string $comment
 * @property boolean $pickup
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $date_at
 * @property int $created_at
 * @property int $updated_at
 * @property string $date
 * @property string $datetime
 *
 * Defined relations:
 * @property \common\modules\payment\models\PaymentType $type
 * @property \common\modules\payment\models\PaymentWithdrawal[] $withdrawals
 * @property \common\modules\content\models\Content $moduleContent
 * @property \common\modules\user\models\User $user
 * @property \common\modules\user\models\User $toUser
 */
class Payment extends ActiveRecord
{
	public $price_tax;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['kind', 'module_type', 'module_id', 'payment_type_id', 'user_id', 'to_user_id', 'provider_id', 'status', 'created_by', 'updated_by', 'date_at', 'created_at', 'updated_at'], 'integer'],
            [['payment_type_id', 'user_id', 'price'], 'required'],
            [['price', 'price_tax', 'tax'], 'number'],
            [['descr', 'provider_error', 'comment'], 'string'],
	        [['pickup'], 'boolean'],
            [['title', 'date', 'datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('payment', 'field_id'),
			'kind' => Yii::t('payment', 'field_kind'),
            'module_type' => Yii::t('payment', 'field_module_type'),
            'module_id' => Yii::t('payment', 'field_module_id'),
            'payment_type_id' => Yii::t('payment', 'field_payment_type_id'),
            'user_id' => Yii::t('payment', 'field_user_id'),
	        'to_user_id' => Yii::t('payment', 'field_to_user_id'),
	        'title' => Yii::t('payment', 'field_title'),
            'price' => Yii::t('payment', 'field_price'),
			'price_tax' => Yii::t('payment', 'field_price_tax'),
			'tax' => Yii::t('payment', 'field_tax'),
            'descr' => Yii::t('payment', 'field_descr'),
			'comment' => Yii::t('payment', 'field_comment'),
	        'pickup' => Yii::t('payment', 'field_pickup'),
            'status' => Yii::t('payment', 'field_status'),
            'date' => Yii::t('payment', 'field_date'),
            'datetime' => Yii::t('payment', 'field_datetime'),
            'created_by' => Yii::t('payment', 'field_created_by'),
            'updated_by' => Yii::t('payment', 'field_updated_by'),
            'date_at' => Yii::t('payment', 'field_date_at'),
            'created_at' => Yii::t('payment', 'field_created_at'),
            'updated_at' => Yii::t('payment', 'field_updated_at'),
            'user_fio' => Yii::t('payment', 'field_user_fio'),
	        'user_lastname' => Yii::t('payment', 'field_user_lastname'),
	        'user_firstname' => Yii::t('payment', 'field_user_firstname'),
	        'user_middlename' => Yii::t('payment', 'field_user_middlename'),
            'user_username' => Yii::t('payment', 'field_user_username'),
            'user_email' => Yii::t('payment', 'field_user_email'),
	        'user_phone' => Yii::t('payment', 'field_user_phone'),
	        'user_address' => Yii::t('payment', 'field_user_address'),
	        'user_telegram' => Yii::t('payment', 'field_user_telegram'),
	        'user_github' => Yii::t('payment', 'field_user_github'),
	        'type_title' => Yii::t('payment', 'field_type_title'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\payment\models\query\PaymentQuery the active query used by this AR class.
     */
    public static function find() {
        $query = new PaymentQuery(get_called_class());
        $query->select([
			self::tableName().'.*',
			'('.self::tableName().'.price * ((100 - '.self::tableName().'.tax) / 100)) AS price_tax'
		]);
        return $query;
    }
	
    public function getWithdrawals() {
    	return $this->hasMany(PaymentWithdrawal::class, ['payment_id' => 'id']);
	}
 
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getModuleContent() {
		return $this->hasOne(Content::class, ['id' => 'module_id'])->alias('moduleContent')->where([]);
	}
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType() {
        return $this->hasOne(PaymentType::class, ['id' => 'payment_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getToUser() {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

    /**
     * @return string
     */
    public function getTitle() {
    	$tmp = [];
    	if ($this->module_type == ModuleType::CONTENT && $this->moduleContent)
    		$tmp[] = $this->moduleContent->title;
    	$tmp[] = $this->type->title;
        return implode(' - ', $tmp);
    }
	
	/**
	 * @return string
	 */
	public function getTitle_user() {
		if ($this->module_type == ModuleType::CONTENT && $this->moduleContent) {
			return ContentType::getLabel($this->moduleContent->type).' - '.$this->moduleContent->title;
		}
		return null;
	}

    /**
     * Get date formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDate($format = 'dd-MM-yyyy') {
        if (!$this->date_at)
            $this->date_at = time();
        return Yii::$app->formatter->asDate($this->date_at, $format);
    }

    /**
     * Set date
     * @param $val
     */
    public function setDate($val) {
        $val .= ' '.date('H:i:s');
        $this->date_at = strtotime($val);
    }

    /**
     * Get datetime formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatetime($format = 'dd-MM-yyyy HH:mm') {
        if (!$this->date_at)
            $this->date_at = time();
        return Yii::$app->formatter->asDatetime($this->date_at, $format);
    }

    /**
     * Set datetime
     * @param $val
     */
    public function setDatetime($val) {
        $val .= ':'.date('s');
        $this->date_at = strtotime($val);
    }
	
	/**
	 * Get price with tax
	 * @return float|int
	 */
    public function getPrice_with_tax_() {
    	return $this->price * ((100 - $this->tax) / 100);
	}
	
	/**
	 * @param $provider
	 * @param $columnName
	 *
	 * @return int
	 */
	public static function getTotal($provider, $columnName) {
		$total = 0;
		foreach ($provider as $item) {
			if ($item->status == StatusWithdrawal::WAIT)
				$total += $item[$columnName];
		}
		return $total;
	}
	
	/**
	 * Send message
	 * @throws \yii\base\InvalidConfigException
	 */
    public function sendMessage() {
    	$subjectUser = null;
    	$subjectAdmin = null;
    	
    	$messageUser = null;
    	$messageAdmin = null;
	    $messageChannel = null;
    	
    	$toIds = [];
    	
	    if ($this->module_type == ModuleType::CONTENT && $this->moduleContent) {
	    	$type = str_replace('type_', '', ContentType::getItem($this->moduleContent->type));
	    	
		    $subjectUser = 'payment_'.$type.'_subject_user';
		    $subjectAdmin = 'payment_'.$type.'_subject_admin';
		    
		    $messageUser = 'payment_'.$type.'_message_user';
		    $messageAdmin = 'payment_'.$type.'_message_admin';
		    $messageChannel = 'payment_'.$type.'_message_channel';
		    
		    $messageParams = [
			    'url_content' => Url::to(['/'.$this->moduleContent->getUriModuleName().'/view', 'id' => $this->moduleContent->id], true),
			    'cost' => Yii::$app->formatter->asCurrency($this->price),
			    'title' => $this->moduleContent->title,
			    'user_from' => $this->user->getAuthorName(true),
			    'user_to' => $this->moduleContent->author->getAuthorName(true),
		    ];
		    $toIds[] = $this->moduleContent->author_id;
	    }
	    
	    $messageParams['url_accruals'] =  Url::to(['/user/payment/accruals'], true);
	    
	    if ($subjectUser && $messageUser && count($toIds)) {
		    Yii::$app->notification->queue($toIds, Yii::t('payment_notify', $subjectUser), Yii::t('payment_notify', $messageUser, $messageParams), 'system');
	    }
	
	    if ($subjectAdmin && $messageAdmin && count(ModuleUser::getInstance()->adminsIds)) {
		    Yii::$app->notification->queue(ModuleUser::getInstance()->adminsIds, Yii::t('payment_notify', $subjectAdmin), Yii::t('payment_notify', $messageAdmin, $messageParams), 'system');
	    }
	    
		$chatIds = ($this->price >= 500) ? TelegramChat::getIdentifiersPayment() : [-1001082506583];
		if ($messageChannel && is_array($chatIds)) {
			Yii::$app->notification->queueTelegramIds($chatIds, Yii::t('payment_notify', $messageChannel, $messageParams));
		}
    }
	
	/**
	 * Calculate sum
	 * @param $provider
	 * @param $field
	 *
	 * @return int
	 */
    static public function totalSum($provider, $field) {
	    $total = 0;
	    foreach ($provider as $item) {
		    $total += $item[$field];
	    }
	    return $total;
    }
	
	/**
	 * Get current tax
	 * @return double
	 */
    static public function tax() {
    	return Yii::$app->getModule('payment')->tax;
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (!$this->date_at) {
            $this->date_at = time();
        }
  
		return parent::beforeSave($insert);
	}

	public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        if ($this->status == Status::PAID) {
            if ($this->module_type == ModuleType::CATALOG_ITEM_ORDER) {

                /** @var CatalogItemOrder $catalogItemOrder */
                $catalogItemOrder = CatalogItemOrder::findById($this->module_id);
                if ($catalogItemOrder) {
                    $catalogItemOrder->status = StatusOrder::PAID;
                    $catalogItemOrder->save();
                }
            }
        }
    }
}
