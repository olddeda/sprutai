<?php
namespace common\modules\catalog\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\query\CatalogFieldGroupQuery;

/**
 * This is the model class for table "{{%catalog_group_field}}".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property-read CatalogField[] $fields
 */
class CatalogFieldGroup extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_field_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title'], 'required'],
            [['status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'unique', 'message' => Yii::t('catalog-field-group', 'error_unique_title')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('catalog-field-group', 'field_id'),
            'title' => Yii::t('catalog-field-group', 'field_title'),
            'status' => Yii::t('catalog-field-group', 'field_status'),
            'created_by' => Yii::t('catalog-field-group', 'field_created_by'),
            'updated_by' => Yii::t('catalog-field-group', 'field_updated_by'),
            'created_at' => Yii::t('catalog-field-group', 'field_created_at'),
            'updated_at' => Yii::t('catalog-field-group', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return CatalogFieldGroupQuery the active query used by this AR class.
     */
    public static function find(): CatalogFieldGroupQuery
    {
        return new CatalogFieldGroupQuery(get_called_class());
    }

    /**
     * Get module type
     * @return int
     */
    public static function moduleType(): int
    {
        return ModuleType::CATALOG_FIELD_GROUP;
    }

    /**
     * @return ActiveQuery
     */
    public function getFields(): ActiveQuery
    {
        return $this->hasMany(CatalogField::class, ['catalog_field_group_id' => 'id'])->where([]);
    }

    /**
     * @param bool $useStatus
     * @return false|int|void
     * @throws Exception
     */
    public function delete($useStatus = true)
    {
        parent::delete($useStatus);

        Yii::$app->db->createCommand('
            UPDATE '.CatalogField::tableName().'
            SET status = :status
            WHERE catalog_field_group_id = :catalog_field_group_id
        ', [
            ':status' => Status::DELETED,
            ':catalog_field_group_id' => $this->id,
        ])->execute();
    }
}