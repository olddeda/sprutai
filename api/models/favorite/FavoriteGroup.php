<?php
namespace api\models\favorite;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\modules\favorite\models\FavoriteGroup as BaseModel;
use common\modules\base\helpers\enum\Status;

/**
 * Class FavoriteGroup
 * @package api\models\favorite
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="module_type", type="integer", description="Тип модуля"),
 *     @OA\Property(property="user_id", type="integer", description="ID пользователя"),
 *     @OA\Property(property="title", type="integer", description="Название"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата изменения")
 * )
 */
class FavoriteGroup extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'module_type',
            'user_id',
            'title',
            'sequence',
            'is_favorite' => function ($data) {
                $moduleId = Yii::$app->request->get('module_id', $data->module_id);
                if ($moduleId) {
                    return in_array($moduleId, ArrayHelper::getColumn($this->items, 'module_id', []));
                }
                return false;
            },
            'count',
            'count_total',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getItems() {
        return $this->hasMany(Favorite::class, ['group_id' => 'id'])->where([])->onCondition([
            Favorite::tableName().'.module_type' => $this->module_type,
            Favorite::tableName().'.user_id' => Yii::$app->user->id,
        ]);
    }

    /**
     * @return bool
     */
    public function beforeValidate() {
        $this->user_id = Yii::$app->user->id;

        return parent::beforeValidate();
    }

    /**
     * @param integer $moduleId
     */
    public function favoriteAdd($moduleId) {
        $model = Favorite::find()->where([
            'group_id' => $this->id,
            'module_type' => $this->module_type,
            'module_id' => $moduleId,
            'user_id' => Yii::$app->user->id,
        ])->one();
        if (!$model) {
            $model = new Favorite();
            $model->group_id = $this->id;
            $model->module_type = $this->module_type;
            $model->module_id = $moduleId;
            $model->user_id = Yii::$app->user->id;
            $this->link('items', $model);
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if ($insert && $this->module_id) {
            $this->favoriteAdd($this->module_id);
        }
    }

    public function afterDelete() {
        parent::afterDelete();

        if ($this->item) {
            $this->item->delete();
        }
    }
}