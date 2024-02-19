<?php
namespace common\modules\content\controllers;

use Yii;
use yii\helpers\Json;
use yii\sphinx\Query;

use common\modules\base\components\Controller;

use common\modules\catalog\models\CatalogItem;

class DefaultController extends Controller
{
    public function actionContentLink($q) {
        $tmp = [];

        /** @var array $ids */
        $ids = (new Query())->select('id')->from('idx_catalog_item')->match(Yii::$app->sphinx->escapeMatchValue($q))->groupBy('id')->limit(20)->column();
        if (count($ids)) {
            $catalogItems = CatalogItem::find()->with(['vendor'])->where(['in', 'id', $ids])->all();
            if ($catalogItems) {
                $items = [];
                foreach ($catalogItems as $item) {
                    $text = $item->vendor->title.' - '.$item->title;
                    if ($item->model) {
                        $text .= ' ('.$item->model.')';
                    }
                    $items[] = [
                        'id' => '[catalogitem:'.$item->id.']',
                        'text' => $text,
                    ];
                }
                $tmp[] = [
                    'text' => 'Товары',
                    'children' => $items,
                ];
            }
        }

        return Json::encode([
            'results' => $tmp
        ]);
    }

}