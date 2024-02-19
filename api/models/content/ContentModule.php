<?php
namespace api\models\content;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use common\modules\content\models\ContentModule as BaseModel;

/**
 * Class ContentModule
 * @package api\models\content
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="content_id", type="string", description="ID материала"),
 *     @OA\Property(property="module_type", type="string", description="Тип модуля"),
 *     @OA\Property(property="module_id", type="string", description="ID материала"),
 * )
 */
class ContentModule extends BaseModel
{
	/**
	 * @inheritdoc
	 */
	public function fields() {
		return [
			'id',
            'content_id',
            'module_type',
            'module_id'
		];
	}
}