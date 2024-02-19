<?php
namespace api\models\seo;

use common\modules\seo\models\Seo as BaseModel;
use common\modules\seo\models\query\SeoQuery;

/**
 * Class Seo
 * @package api\models\seo
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="title", type="string", description="Заголовок"),
 *     @OA\Property(property="h1", type="string", description="H1"),
 *     @OA\Property(property="keywords", type="string", description="Ключевые слова"),
 *     @OA\Property(property="description", type="string", description="Описание"),
 *     @OA\Property(property="slugify", type="string", description="Идентификатор"),
 *     @OA\Property(property="url", type="string", description="Ссылка"),
 * )
 */
class Seo extends BaseModel
{
    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'title',
            'h1',
            'keywords',
            'description',
            'slugify',
            'url'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        return [];
    }
}
