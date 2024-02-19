<?php
namespace common\modules\catalog\models;

use common\modules\base\helpers\enum\Status;
use yii\db\ActiveQuery;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\tag\models\Tag;

use common\modules\catalog\models\query\CatalogFieldGroupTagQuery;
use yii\db\Exception;

/**
 * This is the model class for table "{{%catalog_group_field_tag}}".
 *
 * @property int $catalog_field_group_id
 * @property int $tag_id
 *
 * Defined relations:
 * @property-read CatalogFieldGroup $group
 * @property-read Tag $tag
 */
class CatalogFieldGroupTag extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_field_group_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['catalog_field_group_id', 'tag_id'], 'required'],
            [['catalog_field_group_id', 'tag_id'], 'integer'],
            [['catalog_field_group_id', 'tag_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     * @return CatalogFieldGroupTagQuery the active query used by this AR class.
     */
    public static function find(): CatalogFieldGroupTagQuery
    {
        return new CatalogFieldGroupTagQuery(get_called_class());
    }

    /**
     * Get module type
     * @return int
     */
    public static function moduleType(): int
    {
        return ModuleType::CATALOG_FIELD_GROUP_TAG;
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(CatalogFieldGroup::class, ['id' => 'catalog_field_group_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getTag(): ActiveQuery
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id'])->where([]);
    }

    /**
     * Update links
     *
     * @param array $idsOld
     * @param array $idsNew
     * @param integer $tagId
     * @throws Exception
     */
    public static function updateLinks(array $idsOld, array $idsNew, int $tagId) {
        if (!is_array($idsOld))
            $idsOld = [];
        if (!is_array($idsNew))
            $idsNew = [];

        $idsToAdd = array_values(array_diff($idsNew, $idsOld));
        $idsToRemove = array_values(array_diff($idsOld, $idsNew));

        if (count($idsToRemove)) {
            self::removeLinks($idsToRemove, $tagId);
        }

        if (count($idsToAdd)) {
            self::addLinks($idsToAdd, $tagId);
        }
    }

    /**
     * @param array $ids
     * @param integer $tagId
     *
     * @throws Exception
     */
    public static function addLinks(array $ids, int $tagId)
    {
        $rows = [];
        foreach ($ids as $id) {
            $rows[] = [$id, $tagId];
        }

        self::getDb()->createCommand()->batchInsert(self::tableName(), [
            'catalog_field_group_id', 'tag_id',
        ], $rows)->execute();
    }

    /**
     * @param array $ids
     * @param integer $tagId
     *
     * @throws Exception
     */
    public static function removeLinks(array $ids, int $tagId)
    {
        self::getDb()->createCommand()->delete(self::tableName(), [
            'catalog_field_group_id' => $ids,
            'tag_id' => $tagId,
        ])->execute();

        self::getDb()->createCommand()->update(CatalogItemField::tableName(), [
            'status' => Status::DELETED,
        ], [
            'tag_id' => $tagId
        ])->execute();
    }
}