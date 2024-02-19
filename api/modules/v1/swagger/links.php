<?php
namespace api\models\definitions;

/**
 * @OA\Schema(
 *     @OA\Property(property="self", type="object", description="Ссылка на текущую страницу",
 *         @OA\Property(property="href", type="string"),
 *     ),
 *     @OA\Property(property="first", type="object", description="Ссылка на первую страницу",
 *         @OA\Property(property="href", type="string"),
 *     ),
 *     @OA\Property(property="prev", type="object", description="Ссылка на предыдущую страницу",
 *         @OA\Property(property="href", type="string"),
 *     ),
 *     @OA\Property(property="next", type="object", description="Ссылка на следующую страницу",
 *         @OA\Property(property="href", type="string"),
 *     ),
 *     @OA\Property(property="last", type="object", description="Ссылка на последнюю страницу",
 *         @OA\Property(property="href", type="string"),
 *     )
 * )
 */
class Links {}