<?php
namespace common\modules\cdek\commands;

use Yii;
use yii\console\Controller;

use CdekSDK\Common;
use CdekSDK\Requests;

use common\modules\notification\components\Notification;

use common\modules\catalog\models\CatalogItemOrder;
use common\modules\catalog\helpers\enum\StatusOrder;

use common\modules\cdek\models\City;

/**
 * Class CliController
 * @package common\modules\cdek\commands
 */

class CliController extends Controller
{
    public function actionTest() {
        $id = 968;

        /** @var CatalogItemOrder $catalogItemOrder */
        $catalogItemOrder = CatalogItemOrder::findById($id);
        if (is_null($catalogItemOrder)) {
            return;
        }

        if (is_null($catalogItemOrder->company)) {
            return;
        }

        $order = new Common\Order([
            'Number' => 'SPRUT-'.$catalogItemOrder->id,

            'TariffTypeCode' => $catalogItemOrder->company->cdek_tariff,

            'SendCountryCode' => 'RU',
            'SendCityPostCode' => $catalogItemOrder->company->cdek_postcode,
            'SendCityName' => $catalogItemOrder->company->cdek_city_name,
            'SendCityCode' => $catalogItemOrder->company->cdek_city_id,

            'RecCityPostCode' => $catalogItemOrder->postal_code,
            'RecCountryCode' => 'RU',
            'RecCityName' => $catalogItemOrder->city,

            'RecipientName' => $catalogItemOrder->fio,
            'RecipientEmail' => $catalogItemOrder->email,
            'Phone' => $catalogItemOrder->phone,
            'Comment' => $catalogItemOrder->catalogItem->title,
        ]);

        $order->setAddress(Common\Address::create([
            'Street' => $catalogItemOrder->street,
            'House' => $catalogItemOrder->house,
            'Flat' => $catalogItemOrder->flat,
        ]));

        $package = Common\Package::create([
            'Number' => $catalogItemOrder->id,
            'BarCode' => $catalogItemOrder->id,
            'Weight' => $catalogItemOrder->catalogItem->weight,
            'SizeA' => $catalogItemOrder->catalogItem->length,
            'SizeB' => $catalogItemOrder->catalogItem->width,
            'SizeC' => $catalogItemOrder->catalogItem->height,
        ]);
        $package->addItem(new Common\Item([
            'WareKey' => $catalogItemOrder->catalogItem->id,
            'Cost'    => $catalogItemOrder->price,
            'Weight'  => $catalogItemOrder->catalogItem->width,
            'Comment' => $catalogItemOrder->catalogItem->title,
            'Amount'  => 1,
            'Payment' => 0,
        ]));
        $order->addPackage($package);

        $request = new Requests\DeliveryRequest([
            'Number' => $catalogItemOrder->id,
        ]);
        $request->addOrder($order);

        $response = $catalogItemOrder->company->getCdekClient()->sendDeliveryRequest($request);

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
            if ($number == $catalogItemOrder->id) {
                $catalogItemOrder->delivery_code = $order->getDispatchNumber();
                $catalogItemOrder->save();
            }
        }

    }

    public function actionImportCities() {
        $fileName = __DIR__.'/City_RUS_20200510.csv';
        $data = array_map(function($str) {
            return str_getcsv($str, ';');
        }, file($fileName));
        array_walk($data, function(&$a) use ($data) {
            $a = array_combine($data[0], $a);
        });
        array_shift($data);

        foreach ($data as $item) {
            $model = City::find()->where(['id' => $item['ID']])->one();
            if (!$model) {
                $model = new City();
                $model->id = $item['ID'];
            }
            $model->setAttributes([
                'country_code' => $item['CountryCode'],
                'post_code' => $item['PostCodeList'],
                'city_dd' => $item['cityDD'],
                'full_name' => $item['FullName'],
                'full_name_eng' => $item['EngFullName'],
                'name' => $item['CityName'],
                'name_eng' => $item['EngName'],
                'country' => $item['CountryName'],
                'country_en' => $item['EngCountryName'],
                'region' => $item['OblName'],
                'region_en' => $item['EngOblName'],
                'fias' => $item['FIAS'],
                'fias_full_name' => $item['FullNameFIAS'],
                'kladr' => $item['KLADR'],
                'pvz_code' => $item['pvzCode']
            ]);
            $model->save();
        }
    }
}