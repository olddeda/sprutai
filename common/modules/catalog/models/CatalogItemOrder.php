<?php
namespace common\modules\catalog\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use yii\caching\DbDependency;
use yii\db\ActiveQuery;
use yii\db\Expression;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\company\models\Company;
use common\modules\company\models\CompanyUser;

use common\modules\catalog\models\query\CatalogItemOrderQuery;
use common\modules\catalog\helpers\enum\StatusOrder;
use common\modules\catalog\helpers\enum\DeliveryType;

use common\modules\payment\helpers\enum\Kind;
use common\modules\payment\models\Payment;

/**
 * This is the model class for table "{{%catalog_item_order}}".
 *
 * @property int $id
 * @property int $catalog_item_id
 * @property int $company_id
 * @property double $price
 * @property double $price_with_delivery
 * @property string $fio
 * @property string $email
 * @property string $phone
 * @property string $postal_code
 * @property string $country
 * @property string $country_code
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $block
 * @property string $flat
 * @property string $address
 * @property string $license
 * @property string $comment
 * @property int $delivery_type
 * @property string $delivery_code
 * @property double $delivery_price
 * @property string $delivery_currency
 * @property int $delivery_days_min
 * @property int $delivery_days_max
 * @property int $status
 * @property int $mailed_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CatalogItem $catalogItem
 * @property Company $company
 * @property Payment $payment
 */
class CatalogItemOrder extends ActiveRecord
{
    CONST PAYMENT_TYPE_ID = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%catalog_item_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['catalog_item_id', 'company_id', 'fio', 'email', 'phone', 'price'], 'required'],
            [['delivery_type', 'delivery_code'], 'required', 'when' => function($data) {
                return $data->status == StatusOrder::SENT;
            }],
            [['catalog_item_id', 'company_id', 'status', 'mailed_at', 'created_at', 'updated_at', 'postal_code', 'delivery_type', 'delivery_days_min', 'delivery_days_max'], 'integer'],
            [['price', 'delivery_price'], 'number'],
            [['fio', 'email', 'phone', 'country', 'country_code', 'city', 'street', 'house', 'block', 'flat', 'address', 'license', 'comment', 'delivery_code', 'delivery_currency'], 'string'],
        ];
    }

    /**
     * Get module type
     * @return int
     */
    public static function moduleType() {
        return ModuleType::CATALOG_ITEM_ORDER;
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogItem() {
        return $this->hasOne(CatalogItem::class, ['id' => 'catalog_item_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayment() {
        return $this->hasOne(Payment::class, ['module_id' => 'id'])->onCondition([
            'module_type' => self::moduleType(),
            'kind' => Kind::ACCRUAL,
            'payment_type_id' => self::PAYMENT_TYPE_ID,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return CatalogItemOrderQuery the active query used by this AR class.
     */
    public static function find() {
        return new CatalogItemOrderQuery(get_called_class());
    }

    /**
     * @param string $hash
     * @param array $allowRoles
     *
     * @return array|yii\db\ActiveRecord|null
     */
    public static function findByHash($hash, $allowRoles = []) {
        return self::find()->where(new Expression('MD5(CONCAT(id,catalog_item_id,company_id,created_at))').' = :hash', [
            ':hash' => $hash,
        ])->one();
    }

    /**
     * @inheritDoc
     */
    static public function findBy($id, $except = false, $messageCategory = 'base', $relations = [], $cache = false, $own = false, $conditions = null, $skipFields = [], $callback = null) {
        return self::findByColumn('id', $id, $except, $messageCategory, $relations, $cache, $own, $conditions, $skipFields, $callback);
    }

    /**
     * @inheritDoc
     */
    static public function findByColumn($column, $value, $except = false, $messageCategory = 'base', $relations = [], $cache = false, $own = false, $conditions = null, $skipFields = [], $callback = null) {
        $query = self::find();

        self::prepareQuery($query);

        if (!is_null($callback)) {
            $query = call_user_func($callback, $query);
        }

        $query->andWhere(self::tableName().'.'.$column.' = :'.$column, [
            ':'.$column => $value,
        ]);

        // Add owner user condition
        if ($own) {
            if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
                $ids = CompanyUser::find()
                    ->select('company_id')
                    ->where([
                        'user_id' => Yii::$app->user->id,
                        'status' => Status::ENABLED,
                    ])->column();
                if (!count($ids)) {
                    $ids = [-1];
                }
                $query->andWhere(['in', self::tableName().'.company_id', $ids]);
            }
        }

        if (is_array($relations) && count($relations))
            $query->joinWith($relations);

        if (!is_null($conditions)) {
            $query->andWhere($conditions);
        }

        $model = null;
        if ($cache) {
            $dependency = new DbDependency();
            $dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
            $model = self::getDb()->cache(function ($db) use($query) {
                return $query->one();
            }, Yii::$app->params['cache.duration'], $dependency);
        }
        else
            $model = $query->one();

        if ($model === null && $except)
            throw new NotFoundHttpException(Yii::t($messageCategory, 'error_not_exists'));

        return $model;
    }

    /**
     * @return string
     */
    public function getHash() {
        return md5($this->id.$this->catalog_item_id.$this->company_id.$this->created_at);
    }

    /**
     * @return float
     */
    public function getPrice_with_delivery() {
        return $this->price + $this->delivery_price;
    }

    /**
     * @return array
     */
    public function getPayment_params() {
        $paymentModule = Yii::$app->getModule('payment');
        $process = $paymentModule->start('robokassaZwave', $this->payment->id, $this->payment->price, $this->payment->descr, [
            'hash' => $this->hash,
        ]);

        return [
            'url' => $process->request->url,
            'method' => 'get',
            'data' => $process->request->params,
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert) {
        if ($insert) {
            $this->status = $this->catalogItem->available ? StatusOrder::WAIT : StatusOrder::PREORDER;
            if ($this->status == StatusOrder::PREORDER) {
                $this->mailed_at = time();
            }
        }
        return parent::beforeSave($insert);
    }
}
