<?php
namespace api\models\catalog;

use Yii;

use api\models\tag\TagActivity;

/**
 * Class CatalogItemActivity
 * @package api\models\catalog
 */
class CatalogItemActivity extends CatalogItem
{
    /**
     * @inheritdoc
     */
    public function fields() {
        $result = [
            'id',
            'type' => function ($data) {
                return 'catalog_item';
            },
            'slug',
            'title',
            'model',
            'vendor',
        ];

        return $result;
    }

    /**
     * @return ActiveQuery
     */
    public function getVendor() {
        return $this->hasOne(TagActivity::class, ['id' => 'vendor_id'])->where([]);
    }
}