<?php
namespace api\models\content;

use Yii;

use common\modules\content\models\ContentHistory as BaseModel;

/**
 * Class ContentHistory
 * @package api\models\content
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="content_id", type="integer", description="ID материала"),
 *     @OA\Property(property="user_id", type="integer", description="ID пользователя"),
 *     @OA\Property(property="json", type="object", description="Json"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания")
 * )
 */
class ContentHistory extends BaseModel
{
	/**
	 * @inheritdoc
	 */
	public function fields() {
		return [
			'id',
            'content_id',
            'user_id',
            'json',
            'status',
            'created_at',
		];
	}
}