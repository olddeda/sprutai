<?php
namespace api\models\user\query;

use common\modules\user\models\query\UserQuery as BaseQuery;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use api\models\catalog\CatalogItem;
use api\models\user\User;
use api\models\favorite\Favorite;

/**
 * Class UserQuery
 * @package api\models\user\query
 */
class UserQuery extends BaseQuery
{
    /**
     * @inheritDoc
     */
    public function prepare($builder) {
        $builder = parent::prepare($builder);
        $builder->select(User::tableName().'.*');
        $builder->addSelect([
            'count_catalog_items' => '(
                SELECT COUNT(*) 
                FROM '.Favorite::tableName().' AS f
                LEFT JOIN '.CatalogItem::tableName().' AS c
                ON c.id = f.module_id
                WHERE f.user_id = '.User::tableName().'.id
                AND f.module_type = '.ModuleType::CATALOG_ITEM.'
                AND f.group_id = 4
                AND c.status = '.Status::ENABLED.'
            )'
        ]);
        return $builder;
    }
}