<?php
namespace api\models\catalog\query;

use common\modules\catalog\models\query\CatalogItemOrderQuery as BaseQuery;
use yii\db\Expression;

/**
 * Class CatalogItemOrderQuery
 * @package api\models\catalog\query
 */
class CatalogItemOrderQuery extends BaseQuery
{
    /**
     * @param string $hash
     * @return CatalogItemOrderQuery
     */
    public function findByHash($hash) {
        return $this->andWhere(new Expression('MD5(CONCAT(id,catalog_item_id,company_id,created_at))').' = :hash', [
            ':hash' => $hash,
        ]);
    }
}