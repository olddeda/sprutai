<?php
namespace api\modules\v1\controllers\dashboard;

use api\models\catalog\CatalogItem;
use api\models\catalog\CatalogItemOrder;
use common\modules\base\components\Debug;
use common\modules\catalog\helpers\enum\StatusOrder;
use Yii;
use yii\db\Query;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

//use api\modules\v1\components\Controller;
use common\modules\base\components\Controller;

use api\models\content\Content;
use common\modules\content\helpers\enum\Status as ContentStatus;

/**
 * Class OrderController
 * @package api\modules\v1\controllers\dashboard
 */
class OrderController extends Controller
{
	public function behaviors() {
		return [];
	}

    public function actionIndex() {
	    $items = (new Query())
            ->select('c.catalog_item_id AS id, ci.title')
            ->from(['c' => CatalogItemOrder::tableName()])
            ->leftJoin(['ci' => CatalogItem::tableName()], 'ci.id = c.catalog_item_id')
            ->distinct()
            ->all()
        ;

	    foreach ($items as $key => $item) {

	        $select = [];
	        foreach ([StatusOrder::PREORDER, StatusOrder::WAIT, StatusOrder::PENDING, StatusOrder::PAID, StatusOrder::SENT, StatusOrder::COMPLETE] as $status) {
	            $select[] = '(
	                SELECT COUNT(*) 
	                FROM '.CatalogItemOrder::tableName().' AS t 
	                WHERE t.catalog_item_id = '.$item['id'].' 
	                AND status = '.$status.'
	            ) AS '.str_replace('status_order_', '', StatusOrder::getItem($status));
            }

	        $stat = (new Query())
                ->select([
                    'COUNT(*) AS total',
                ])
                ->addSelect($select)
                ->from(['cio' => CatalogItemOrder::tableName()])
                ->where(['cio.catalog_item_id' => $item['id']])
                ->andWhere([
                    'not in',
                    'cio.status',
                    [StatusOrder::CANCELED]
                ])
                ->one()
            ;
	        $items[$key]['stat'] = $stat;
        }

        return $items;
    }
}
