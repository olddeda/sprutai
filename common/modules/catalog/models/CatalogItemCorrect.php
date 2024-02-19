<?php
namespace common\modules\catalog\models;

use common\modules\catalog\helpers\enum\CorrectAction;
use common\modules\catalog\helpers\enum\CorrectType;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\tag\models\Tag;

/**
 * This is the model class for table "{{%catalog_item_correct}}".
 *
 * @property int $id
 * @property int $catalog_item_id
 * @property int $type
 * @property int $action
 * @property string $value
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property-read CatalogItem $item
 */
class CatalogItemCorrect extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_item_correct}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['catalog_item_id', 'type', 'action', 'status'], 'required'],
            [['catalog_item_id', 'type', 'action', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'string', 'max' => 1000],
            ['catalog_item_id', 'exist', 'targetClass' => CatalogItem::class, 'targetAttribute' => ['catalog_item_id' => 'id']],
        ];
    }

    /**
     * Get module type
     * @return int
     */
    public static function moduleType(): int
    {
        return ModuleType::CATALOG_ITEM_CORRECT;
    }

    /**
     * @return ActiveQuery
     */
    public function getItem(): ActiveQuery
    {
        return $this->hasOne(CatalogItem::class, ['id' => 'catalog_item_id'])->where([]);
    }
}