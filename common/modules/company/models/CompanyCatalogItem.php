<?php
namespace common\modules\company\models;

use Yii;
use yii\db\ActiveQuery;

use common\modules\base\components\ActiveRecord;

use common\modules\catalog\models\CatalogItem;

use common\modules\company\models\query\CompanyCatalogItemQuery;

/**
 * This is the model class for table "{{%company_catalog_item}}".
 *
 * @property int $id
 * @property int $company_id
 * @property int $catalog_item_id
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property Company $company
 * @property CatalogItem $catalogItem
 */
class CompanyCatalogItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%company_catalog_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['company_id', 'catalog_item_id'], 'required'],
            [['company_id', 'catalog_item_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['catalog_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => CatalogItem::class, 'targetAttribute' => ['catalog_item_id' => 'id']],
			['catalog_item_id', 'unique', 'targetAttribute' => ['company_id', 'catalog_item_id']]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogItem() {
        return $this->hasOne(CatalogItem::class, ['id' => 'catalog_item_id']);
    }

    /**
     * {@inheritdoc}
     * @return CompanyCatalogItemQuery the active query used by this AR class.
     */
    public static function find() {
        return new CompanyCatalogItemQuery(get_called_class());
    }
}
