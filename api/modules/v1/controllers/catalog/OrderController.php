<?php
namespace api\modules\v1\controllers\catalog;

use api\models\catalog\CatalogItemOrder;
use api\models\catalog\search\CatalogItemOrderSearch;
use api\modules\v1\components\actions\IndexAction;
use api\modules\v1\components\ActiveController as Controller;
use CdekSDK\Requests;
use common\modules\catalog\helpers\enum\StatusOrder;
use common\modules\notification\components\Notification;
use common\modules\payment\helpers\enum\Kind;
use common\modules\payment\helpers\enum\Status;
use common\modules\payment\models\Payment;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class OrderController
 * @package api\modules\v1\controllers
 */
class OrderController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\catalog\CatalogItemOrder';

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['create', 'view-link', 'update-link', 'pay-link'],
            ],
            'access' => [
                'except' => ['create', 'view-link', 'update-link', 'pay-link'],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['dataFilter'] = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => 'api\models\catalog\search\CatalogItemOrderSearch',
        ];

        $actions['view']['findModel'] = [$this, 'findModel'];
        $actions['update']['findModel'] = [$this, 'findModel'];
        $actions['delete']['findModel'] = [$this, 'findModel'];

        unset($actions['create']);

        return $actions;
    }

    /**
     * @param IndexAction $action
     * @param $filter
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(IndexAction $action, $filter) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $searchModel = new CatalogItemOrderSearch();
        return $searchModel->search($requestParams);
    }


    /**
     * @return CatalogItemOrder|array
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     */
    public function actionCreate() {

        $dateStart = new \DateTime('2020-08-06 10:00:00');
        $dateNow = new \DateTime();

        //if ($dateStart > $dateNow) {
        //    return ["error" => "date"];
        //}

        /* @var $model CatalogItemOrder */
        $model = new CatalogItemOrder();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->price = $model->catalogItem->price;
        if ($model->save()) {

            $message = Yii::t('catalog-notification', 'order_create', [
                'id' => $model->id,
                'fio' => $model->fio,
                'phone' => $model->phone,
                'email' => $model->email,
                'catalog_item' => $model->catalogItem->title,
                'status' => StatusOrder::getLabel($model->status),
            ]);

            /** @var Notification $notification */
            $notification = Yii::$app->get('notification');
            $notification->queueTelegramIds([
                357615556,
                269033981
            ], $message);

            $notification->queueEmails([
                'info@z-wave.ru'
            ], Yii::t('catalog-notification', 'order_create_subject', [
                'id' => $model->id
            ]), $message, [
                'layouts' => [
                    'text' => '@common/modules/notification/tpl/layouts/text_empty',
                    'html' => '@common/modules/notification/tpl/layouts/html_empty',
                ],
            ]);
        }
        else if (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param string $hash
     *
     * @return CatalogItemOrder|null
     * @throws NotFoundHttpException
     */
    public function actionViewLink($hash) {
        $model = $this->findModelByHash($hash);
        $model->returnHash = true;
        return $model;
    }

    /**
     * @param string $hash
     *
     * @return CatalogItemOrder|null
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdateLink($hash) {
        $model = $this->findModelByHash($hash);

        $oldAddress = $model->address;
        $oldStatus = $model->status;

        $params = Yii::$app->getRequest()->getBodyParams();
        unset($params['status']);

        $model->load($params, '');

        if ($model->save()) {
            if (($oldStatus == StatusOrder::ADDRESS && $model->status == StatusOrder::PENDING)) {
                $message = Yii::t('catalog-notification', 'order_address_complete', [
                    'id' => $model->id,
                    'fio' => $model->fio,
                    'phone' => $model->phone,
                    'email' => $model->email,
                    'address' => $model->address,
                    'catalog_item' => $model->catalogItem->title,
                    'status' => StatusOrder::getLabel($model->status),
                ]);

                /** @var Notification $notification */
                $notification = Yii::$app->get('notification');
                $notification->queueEmails([
                    'info@z-wave.ru'
                ], Yii::t('catalog-notification', 'order_address_complete_subject', [
                    'id' => $model->id
                ]), $message, [
                    'layouts' => [
                        'text' => '@common/modules/notification/tpl/layouts/text_empty',
                        'html' => '@common/modules/notification/tpl/layouts/html_empty',
                    ],
                ]);
            }
        }
        else if (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param string $hash
     *
     * @return CatalogItemOrder|null
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function actionPayLink($hash) {

        /** @var CatalogItemOrder $model */
        $model = $this->findModelByHash($hash);

        $params = Yii::$app->getRequest()->getBodyParams();
        unset($params['status']);

        $model->load($params, '');
        $model->status = StatusOrder::PAID_WAIT;

        if ($model->country_code == "RU") {
            $company = $model->catalogItem->company;

            $request = new Requests\CalculationAuthorizedRequest();
            $request
                ->setSenderCityPostCode($company->cdek_postcode)
                ->setReceiverCityPostCode($model->postal_code)
                ->setTariffId($company->cdek_tariff)
                ->addPackage([
                    'weight' => $model->catalogItem->weight,
                    'length' => $model->catalogItem->length,
                    'width'  => $model->catalogItem->width,
                    'height' => $model->catalogItem->height,
                ])
            ;

            $response = $company->getCdekClient()->sendCalculationRequest($request);
            if ($response->hasErrors()) {
                $tmp = [];
                foreach ($response->getMessages() as $message) {
                    $tmp[] = [
                        'code' => $message->getErrorCode(),
                        'message' => $message->getMessage(),
                    ];
                }
                return [
                    'error' => $tmp
                ];
            }

            $model->delivery_price = $response->getPrice() + 100;
            $model->delivery_currency = $response->getCurrency();
            $model->delivery_days_min = $response->getDeliveryPeriodMin() + 2;
            $model->delivery_days_max = $response->getDeliveryPeriodMax() + 2;
        }

        if ($model->save()) {
            if (is_null($model->payment)) {
                $payment = new Payment();
                $payment->kind = Kind::ACCRUAL;
                $payment->module_type = CatalogItemOrder::moduleType();
                $payment->module_id = $model->id;
                $payment->payment_type_id = CatalogItemOrder::PAYMENT_TYPE_ID;
                $payment->user_id = 1;
                $payment->status = Status::WAIT;
                $payment->date_at = time();
                $payment->price = $model->price_with_delivery;
                $payment->descr = $payment->type->title.' - '.$model->catalogItem->title;
                if (!$payment->validate()) {
                    return $payment->errors;
                }
                $model->link('payment', $payment);
            }

        }
        else if (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param integer $id
     * @param bool $owner
     *
     * @return array|ActiveRecord|null
     */
    public function findModel($id, $owner = false) {
        return CatalogItemOrder::findBy($id, true, 'catalog-item-order', [], false, true);
    }

    /**
     * @param string $hash
     *
     * @return CatalogItemOrder|null
     * @throws NotFoundHttpException
     */
    public function findModelByHash($hash) {
        $model = CatalogItemOrder::findByHash($hash);
        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('catalog-item-order', 'error_not_exists'));
        }
        return $model;
    }
}
