<?php
namespace api\models\content\query;

use api\models\catalog\CatalogItem;

use api\models\content\Content;
use api\models\content\ContentModule;

use common\modules\base\components\ArrayHelper;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\content\helpers\enum\Status;
use common\modules\content\models\query\ContentQuery as BaseContentQuery;

/**
 * This is the ActiveQuery class for [[\api\models\content\Content]].
 *
 * @see \api\models\content\Content
 */
class ContentQuery extends BaseContentQuery
{
    public function withData() {
        $this->addSelect([
            '(
                SELECT COUNT(*) 
                FROM '.ContentModule::tableName().' AS cm
                LEFT JOIN '.CatalogItem::tableName().' AS ci
                ON ci.id = cm.module_id
                WHERE cm.content_id = '.Content::tableName().'.id
                AND cm.module_type = '.ModuleType::CATALOG_ITEM.'
                AND ci.status = '.Status::ENABLED.'
            ) AS count_catalog_items'
        ]);
        return $this;
    }
}