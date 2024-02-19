<?php
namespace common\modules\catalog\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\catalog\models\query\CatalogFieldQuery;

/**
 * This is the model class for table "{{%catalog_field}}".
 *
 * @property int $id
 * @property int $catalog_field_group_id
 * @property int $type
 * @property int $format
 * @property string $title
 * @property string $identifier
 * @property string $unit
 * @property int $sequence
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property-read CatalogFieldGroup $group
 */
class CatalogField extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_field}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['catalog_field_group_id', 'title', 'identifier', 'sequence', 'status'], 'required'],
            [['catalog_field_group_id', 'type', 'format', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['title', 'identifier', 'unit'], 'string', 'max' => 255],
            ['catalog_field_group_id', 'exist', 'targetClass' => CatalogFieldGroup::class, 'targetAttribute' => ['catalog_field_group_id' => 'id'], 'message' => Yii::t('catalog-field', 'error_group_not_exists')],
            ['title', 'unique', 'message' => Yii::t('catalog-field', 'error_unique_title')],
            ['identifier', 'unique', 'message' => Yii::t('catalog-field', 'error_unique_identifier')],
            ['identifier', 'match', 'pattern' => '/^[a-z0-9_]+$/', 'message' => Yii::t('catalog-field', 'error_invalid_identifier')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('catalog-field', 'field_id'),
            'catalog_field_group_id' => Yii::t('catalog-field', 'field_catalog_field_group_id'),
            'type' => Yii::t('catalog-field', 'field_type'),
            'format' => Yii::t('catalog-field', 'field_format'),
            'title' => Yii::t('catalog-field', 'field_title'),
            'identifier' => Yii::t('catalog-field', 'field_identifier'),
            'unit' => Yii::t('catalog-field', 'field_unit'),
            'status' => Yii::t('catalog-field', 'field_status'),
            'created_by' => Yii::t('catalog-field', 'field_created_by'),
            'updated_by' => Yii::t('catalog-field', 'field_updated_by'),
            'created_at' => Yii::t('catalog-field', 'field_created_at'),
            'updated_at' => Yii::t('catalog-field', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return CatalogFieldQuery the active query used by this AR class.
     */
    public static function find(): CatalogFieldQuery
    {
        return new CatalogFieldQuery(get_called_class());
    }

    /**
     * Get module type
     * @return int
     */
    public static function moduleType(): int
    {
        return ModuleType::CATALOG_FIELD;
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(CatalogFieldGroup::class, ['id' => 'catalog_field_group_id'])->where([]);
    }

    /**
     * @return bool
     * @throws InvalidConfigException
     */
    public function beforeValidate(): bool
    {
        $this->identifier = strtolower($this->identifier);

        if (is_null($this->sequence)) {
            $this->sequence = self::lastSequence([
                'catalog_field_group_id' => $this->catalog_field_group_id
            ]);
        }

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        CatalogItemField::updateAll([
            'type' => $this->type,
            'format' => $this->format,
            'title' => $this->title,
            'identifier' => $this->identifier,
            'unit' => $this->unit,
            'sequence' => $this->sequence,
        ], [
            'catalog_field_id' => $this->id,
        ]);
    }
}