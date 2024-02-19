<?php
namespace api\models\catalog\forms;

use Yii;
use yii\base\Model;

use api\models\catalog\CatalogField;

/**
 *
 * @OA\RequestBody(
 *     request="CatalogFieldSwap",
 *     required=true,
 *     description="Замена порядковых номеров у полей",
 *     @OA\JsonContent(
 *         @OA\Property(property="from_id",type="integer", description="ID первого поля"),
 *         @OA\Property(property="to_id",type="integer", description="ID второго поля"),
 *         example={
 *             "from_id": 1,
 *             "to_id": 2
 *         }
 *     )
 * )
 */

/**
 * Class CatalogFieldSwapForm
 *
 * @property integer $from_id
 * @property integer $to_id
 *
 * @property-read CatalogField $from
 * @property-read CatalogField $to
 */
class CatalogFieldSwapForm extends Model
{
    /**
     * @var integer
     */
    public $from_id;

    /**
     * @var integer
     */
    public $to_id;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['from_id', 'to_id'], 'required'],
            [['from_id', 'to_id'], 'integer'],
            ['from_id', 'exist', 'targetClass' => CatalogField::class, 'targetAttribute' => ['from_id' => 'id'], 'message' => Yii::t('catalog-field', 'error_field_not_exists')],
            ['to_id', 'exist', 'targetClass' => CatalogField::class, 'targetAttribute' => ['to_id' => 'id'], 'message' => Yii::t('catalog-field', 'error_field_not_exists')],
            ['to_id', 'validateGroup'],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateGroup($attribute) {
        if ($this->from && $this->to && $this->from->catalog_field_group_id != $this->to->catalog_field_group_id) {
            $this->addError($attribute, Yii::t('catalog-field', 'error_swap_not_equal_group'));
        }
    }

    /**
     * @return CatalogField
     */
    public function getFrom(): CatalogField
    {
        return CatalogField::findById($this->from_id, true, 'catalog-field');
    }

    /**
     * @return CatalogField
     */
    public function getTo(): CatalogField
    {
        return CatalogField::findById($this->to_id, true, 'catalog-field');
    }

    /**
     * @return bool
     */
    public function run(): bool {
        $from = $this->getFrom();
        $to = $this->getTo();

        $fromSequence = $from->sequence;
        $toSequence = $to->sequence;

        $from->sequence = $toSequence;
        $to->sequence = $fromSequence;

        return $from->save() && $to->save();
    }
}