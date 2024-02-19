<?php
namespace api\modules\v1\controllers;

use api\models\cdek\CdekCalculation;
use api\models\cdek\forms\CdekCalculationForm;
use api\modules\v1\components\Controller;
use CdekSDK\Requests;
use common\modules\base\helpers\JsonMapper;
use common\modules\catalog\models\CatalogItem;
use ReflectionException;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class CdekController extends Controller {

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'except' => ['calculate'],
            ],
            'access' => [
                'except' => ['calculate'],
            ],
        ]);
    }


    /**
     * @OA\Post(path="/cdek/calculate",
     *     tags={"cdek"},
     *     summary="Расчет стоимости доставки",
     *     description="Определяет стоимость доставки товара для указанного адреса",
     *     operationId="cdek_calculate",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CdekCalculationForm"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(@OA\Property(property="item", ref="#/components/schemas/CdekCalculation")),
     *         @OA\XmlContent(ref="#/components/schemas/CdekCalculation")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Товар с таким id не найден"
     *     ),
     * )
     * @throws ReflectionException|InvalidConfigException
     */
    public function actionCalculate() {

        /** @var CdekCalculationForm $form */
        $form = (new JsonMapper())->map(Yii::$app->getRequest()->getBodyParams(), new CdekCalculationForm());

        /** @var CatalogItem $catalogItem */
        $catalogItem = CatalogItem::findById($form->catalog_item_id, true, 'catalog-item');

        $company = $catalogItem->company;

        $request = new Requests\CalculationAuthorizedRequest();
        $request
            ->setSenderCityPostCode($company->cdek_postcode)
            ->setReceiverCityPostCode($form->postal_code)
            ->setTariffId($company->cdek_tariff)
            ->addPackage([
                'weight' => $catalogItem->weight,
                'length' => $catalogItem->length,
                'width'  => $catalogItem->width,
                'height' => $catalogItem->height,
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

        $calculation = new CdekCalculation();
        $calculation->days_min = $response->getDeliveryPeriodMin() + 2;
        $calculation->days_max = $response->getDeliveryPeriodMax() + 2;
        $calculation->price = $response->getPrice() + 100;
        $calculation->currency = $response->getCurrency();

        return $calculation;
    }
}