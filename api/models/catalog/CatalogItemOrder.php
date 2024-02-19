<?php
namespace api\models\catalog;


use api\models\catalog\query\CatalogItemOrderQuery;
use CdekSDK\Common;
use CdekSDK\Requests;
use common\modules\catalog\helpers\enum\DeliveryType;
use common\modules\catalog\helpers\enum\StatusOrder;
use common\modules\catalog\models\CatalogItemOrder as BaseModel;
use common\modules\hub\helpers\enum\LicenseType;
use common\modules\hub\models\HubLicense;
use common\modules\notification\components\Notification;
use common\modules\payment\models\Payment;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class CatalogItemOrder
 * @package api\models\catalog
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="catalog_item_id", type="string", description="ID устройства"),
 *     @OA\Property(property="company_id", type="integer", description="ID компании"),
 *     @OA\Property(property="price", type="double", description="Стоимость"),
 *     @OA\Property(property="fio", type="string", description="Имя и фамилия"),
 *     @OA\Property(property="phone", type="string", description="Телефон"),
 *     @OA\Property(property="email", type="string", description="E-mail"),
 *     @OA\Property(property="postal_code", type="integer", description="Почтовый индекс"),
 *     @OA\Property(property="country_code", type="string", description="Код страны"),
 *     @OA\Property(property="country", type="string", description="Страна"),
 *     @OA\Property(property="city", type="string", description="Город"),
 *     @OA\Property(property="street", type="string", description="Улица"),
 *     @OA\Property(property="house", type="string", description="Дом"),
 *     @OA\Property(property="block", type="string", description="Корпус"),
 *     @OA\Property(property="flat", type="string", description="Квартира"),
 *     @OA\Property(property="address", type="string", description="Адрес"),
 *     @OA\Property(property="comment", type="string", description="Комментарий"),
 *     @OA\Property(property="delivery_type", type="integer", description="Cлужба доставки"),
 *     @OA\Property(property="delivery_code", type="integer", description="Код отслеживания доставки"),
 *     @OA\Property(property="delivery_price", type="double", description="Стоимость доставки"),
 *     @OA\Property(property="delivery_currency", type="string", description="Валюта стоимости доставки"),
 *     @OA\Property(property="delivery_days_min", type="integer", description="Минимальный срок доставки"),
 *     @OA\Property(property="delivery_days_max", type="integer", description="Максимальный срок доставки"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата обновления"),
 *     @OA\Property(property="catalogItem", ref="#/components/schemas/CatalogItem", description="Устройство")
 * )
 */

class CatalogItemOrder extends BaseModel
{
    /**
     * @var bool
     */
    public $returnHash = false;

    /**
     * @inheritdoc
     */
    public function fields() {
        $result = [
            'id',
            'catalog_item_id',
            'company_id',
            'price',
            'fio',
            'email',
            'phone',
            'postal_code',
            'country_code',
            'country',
            'city',
            'street',
            'house',
            'block',
            'flat',
            'address',
            'license',
            'comment',
            'delivery_type',
            'delivery_code',
            'delivery_price',
            'delivery_currency',
            'delivery_days_min',
            'delivery_days_max',
            'status' => function ($data) {
                return (int)$data->status;
            },
            'created_at',
            'updated_at',
            'payment_params' => function ($data) {
                return $data->status == StatusOrder::PAID_WAIT ? $data->getPayment_params() : [];
            },
            'payment_id' => function ($data) {
                return $data->payment ? $data->payment->id : null;
            },
            'hash',
        ];
        return $result;
    }

    public function extraFields() {
        return [
            'catalogItem'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogItem() {
        return $this->hasOne(CatalogItem::class, ['id' => 'catalog_item_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayment() {
        return $this->hasOne(Payment::class, ['module_id' => 'id'])->onCondition([
            'module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return string[]
     */
    public function getSupportChatLink() {
        if ($this->catalog_item_id == 2305) {
            return [
                'title' => 'https://t.me/joinchat/EAkh_VXeO-ENGMZqgiRuxw',
                'url' => 'tg://join?invite=EAkh_VXeO-ENGMZqgiRuxw',
            ];
        }
        return [
            'title' => 'https://t.me/joinchat/EAkh_Up9av_G_H4PFUitoA',
            'url' => 'tg://join?invite=EAkh_Up9av_G_H4PFUitoA'
        ];
    }

    /**
     * {@inheritdoc}
     * @return CatalogItemOrderQuery the active query used by this AR class.
     */
    public static function find() {
        return new CatalogItemOrderQuery(get_called_class());
    }

    /**
     * Send to cdek
     */
    private function _sendCdek() {

        $order = new Common\Order([
            'Number' => 'SPRUT-'.$this->id,

            'TariffTypeCode' => $this->company->cdek_tariff,

            'SendCountryCode' => 'RU',
            'SendCityPostCode' => $this->company->cdek_postcode,
            'SendCityName' => $this->company->cdek_city_name,
            'SendCityCode' => $this->company->cdek_city_id,

            'RecCityPostCode' => $this->postal_code,
            'RecCountryCode' => 'RU',
            'RecCityName' => $this->city,

            'RecipientName' => $this->fio,
            'RecipientEmail' => $this->email,
            'Phone' => $this->phone,
            'Comment' => $this->catalogItem->title,
        ]);

        $order->setAddress(Common\Address::create([
            'Street' => $this->street,
            'House' => $this->house,
            'Flat' => $this->flat,
        ]));

        $package = Common\Package::create([
            'Number' => $this->id,
            'BarCode' => $this->id,
            'Weight' => $this->catalogItem->weight,
            'SizeA' => $this->catalogItem->length,
            'SizeB' => $this->catalogItem->width,
            'SizeC' => $this->catalogItem->height,
        ]);
        $package->addItem(new Common\Item([
            'WareKey' => $this->catalogItem->id,
            'Cost'    => $this->price,
            'Weight'  => $this->catalogItem->width,
            'Comment' => $this->catalogItem->title,
            'Amount'  => 1,
            'Payment' => 0,
        ]));
        $order->addPackage($package);

        $request = new Requests\DeliveryRequest([
            'Number' => $this->id,
        ]);
        $request->addOrder($order);

        $response = $this->company->getCdekClient()->sendDeliveryRequest($request);

        if ($response->hasErrors()) {
            foreach ($response->getErrors() as $order) {
                print_r($order->getMessage());
            }

            foreach ($response->getMessages() as $message) {
                print_r($message);
            }
        }

        foreach ($response->getOrders() as $order) {
            $number = str_replace('SPRUT-', '', $order->getNumber());
            if ($number == $this->id) {
                $this->delivery_code = $order->getDispatchNumber();
                $this->status = StatusOrder::SENT;
                $this->save();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function afterValidate() {
        parent::afterValidate();

        if (!is_null($this->address) && $this->status == StatusOrder::ADDRESS) {
            if (is_null($this->delivery_price)) {
                $this->status = StatusOrder::PENDING;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert) {
        if (isset($this->oldAttributes['status']) && $this->oldAttributes['status'] != $this->status) {
            if ($this->status == StatusOrder::PAID) {
                if (in_array($this->catalog_item_id, [1809, 2305, 2570]) && is_null($this->license)) {
                    $license = new HubLicense();
                    $license->type = ($this->catalog_item_id == 2305) ? LicenseType::ZWAVE : LicenseType::ZIGBEE;
                    $license->code = HubLicense::generateCode();
                    $license->status = 1;
                    if ($license->save()) {
                        $this->license = $license->code;
                    }
                }
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        $links = [
            DeliveryType::CDEK => 'https://cdek.ru',
            DeliveryType::RUSSIAN_POST => 'https://www.pochta.ru',
            DeliveryType::EMS => 'https://www.pochta.ru/emspost/',
            DeliveryType::INTEGRAL => 'https://integraldos.ru/'
        ];

        $params = [
            'id' => $this->id,
            'fio' => $this->fio,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'catalog_item' => $this->catalogItem->title,
            'catalog_item_url' => 'https://v2.sprut.ai/catalog/item/'.$this->catalogItem->seo->slugify,
            'delivery_type' => DeliveryType::getLabel($this->delivery_type),
            'delivery_type_link' => $links[$this->delivery_type],
            'delivery_code' => $this->delivery_code,
            'status' => StatusOrder::getLabel($this->status),
            'order_link' => 'https://spruthub.ru/stick/order/'.$this->getHash(),
        ];

        if (!$insert) {
            $oldStatus = (isset($changedAttributes['status'])) ? $changedAttributes['status'] : null;
            if (
                $oldStatus != $this->status &&
                !($oldStatus == StatusOrder::ADDRESS && $this->status == StatusOrder::PENDING) &&
                $this->status != StatusOrder::DELETED &&
                $this->status != StatusOrder::WAIT
            ) {

                $messageKey = ($this->status == StatusOrder::SENT) ? 'order_changed_sent' : 'order_changed';

                if ($this->status == StatusOrder::SENT) {
                    if ($this->delivery_type == DeliveryType::INTEGRAL) {
                        $params['delivery_link'] = Yii::t('catalog-notification', 'order_changed_set_integral', [
                            'code' => $this->delivery_code,
                        ]);
                    }
                    else {
                        $links = [
                            DeliveryType::CDEK => 'https://cdek.ru/tracking',
                            DeliveryType::RUSSIAN_POST => 'https://www.pochta.ru/tracking',
                            DeliveryType::EMS => 'https://www.pochta.ru/emspost/',
                        ];
                        $params['delivery_link'] = Yii::t('catalog-notification', 'order_changed_set_link', [
                            'code' => $this->delivery_code,
                            'link' => $links[$this->delivery_type]
                        ]);
                    }
                }

                $message = Yii::t('catalog-notification', $messageKey, $params);

                /** @var Notification $notification */
                $notification = Yii::$app->get('notification');
                $notification->queueEmails([
                    $this->email,
                ], Yii::t('catalog-notification', 'order_changed_subject', [
                    'id' => $this->id,
                    'status' => StatusOrder::getLabel($this->status),
                ]), $message, [
                    'layouts' => [
                        'text' => '@common/modules/notification/tpl/layouts/text_empty',
                        'html' => '@common/modules/notification/tpl/layouts/html_empty',
                    ],
                ]);

                if ($this->status == StatusOrder::PAID && in_array('license', array_keys($changedAttributes))) {
                    $license = HubLicense::find()->where(['code' => $this->license])->one();
                    if ($license) {
                        $supportChatLink = $this->getSupportChatLink();
                        $message = Yii::t('catalog-notification', 'order_license', [
                            'fio' => $this->fio,
                            'type' => LicenseType::getLabel($license->type),
                            'code' => $license->code,
                            'support_chat_title' => $supportChatLink['title'],
                            'support_chat_url' => $supportChatLink['url'],
                        ]);

                        $notification->queueEmails([
                            $this->email,
                        ], Yii::t('catalog-notification', 'order_license_subject', [
                            'type' => LicenseType::getLabel($license->type),
                        ]), $message, [
                            'layouts' => [
                                'text' => '@common/modules/notification/tpl/layouts/text_empty',
                                'html' => '@common/modules/notification/tpl/layouts/html_empty',
                            ],
                        ]);
                    }
                }

                if ($oldStatus == StatusOrder::PAID_WAIT && $this->status == StatusOrder::PAID) {
                    if ($this->delivery_type == DeliveryType::CDEK) {
                        $this->_sendCdek();
                    }
                }
            }
        }
        else {
            if ($this->status === StatusOrder::PREORDER) {
                $count = \common\modules\catalog\models\CatalogItemOrder::find()
                    ->with(['catalogItem'])
                    ->andWhere(['status' => StatusOrder::PREORDER])
                    ->orderBy(['id' => SORT_ASC])
                    ->count()
                ;

                $params['num'] = $count + 1;

                $subject = Yii::t('catalog-notification', 'order_preorder_subject', $params);
                $message = Yii::t('catalog-notification', 'order_preorder_num', $params);

                /** @var Notification $notification */
                $notification = Yii::$app->get('notification');
                $notification->queueEmails([
                    $this->email,
                ], $subject, $message, [
                    'layouts' => [
                        'text' => '@common/modules/notification/tpl/layouts/text_empty',
                        'html' => '@common/modules/notification/tpl/layouts/html_empty',
                    ],
                ]);
            }
        }
    }
}