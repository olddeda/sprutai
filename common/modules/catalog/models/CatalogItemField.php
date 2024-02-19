<?php
namespace common\modules\catalog\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\tag\models\Tag;

use common\modules\catalog\models\query\CatalogItemFieldQuery;

/**
 * This is the model class for table "{{%catalog_item_field}}".
 *
 * @property int $catalog_item_id
 * @property int $catalog_field_group_id
 * @property int $catalog_field_id
 * @property int $catalog_tag_id
 * @property int $type
 * @property int $format
 * @property string $name
 * @property string $value
 * @property string $title
 * @property string $identifier
 * @property string $unit
 * @property int $sequence
 * @property int $status
 *
 * Defined relations:
 * @property-read CatalogItem $item
 * @property-read CatalogItemFieldGroup $fieldGroup
 * @property-read CatalogItemField $field
 * @property-read Tag $tag
 */
class CatalogItemField extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_item_field}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['catalog_item_id', 'catalog_field_group_id', 'catalog_field_id', 'tag_id', 'type', 'format', 'name', 'value', 'title', 'identifier', 'sequence', 'status'], 'required'],
            [['catalog_item_id', 'catalog_field_group_id', 'catalog_field_id', 'tag_id', 'type', 'format', 'sequence', 'status'], 'integer'],
            [['name', 'value', 'title', 'identifier', 'unit'], 'string', 'max' => 255],
            ['catalog_item_id', 'exist', 'targetClass' => CatalogItem::class, 'targetAttribute' => ['catalog_item_id' => 'id'], 'message' => Yii::t('catalog-item-field', 'error_item_not_exists')],
            ['catalog_field_group_id', 'exist', 'targetClass' => CatalogItemFieldGroup::class, 'targetAttribute' => ['catalog_field_group_id' => 'id'], 'message' => Yii::t('catalog-item-field', 'error_field_group_not_exists')],
            ['catalog_field_id', 'exist', 'targetClass' => CatalogItemField::class, 'targetAttribute' => ['catalog_field_id' => 'id'], 'message' => Yii::t('catalog-item-field', 'error_field_not_exists')],
            ['tag_id', 'exist', 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id'], 'message' => Yii::t('catalog-item-field', 'error_tag_not_exists')],
        ];
    }

    /**
     * {@inheritdoc}
     * @return CatalogItemFieldQuery the active query used by this AR class.
     */
    public static function find(): CatalogItemFieldQuery
    {
        return new CatalogItemFieldQuery(get_called_class());
    }

    /**
     * Get module type
     * @return int
     */
    public static function moduleType(): int
    {
        return ModuleType::CATALOG_ITEM_FIELD;
    }

    /**
     * @return ActiveQuery
     */
    public function getItem(): ActiveQuery
    {
        return $this->hasOne(CatalogItem::class, ['id' => 'catalog_item_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFieldGroup(): ActiveQuery
    {
        return $this->hasOne(CatalogFieldGroup::class, ['id' => 'catalog_field_group_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getField(): ActiveQuery
    {
        return $this->hasOne(CatalogField::class, ['id' => 'catalog_field_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getTag(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id'])->where([]);
    }
}