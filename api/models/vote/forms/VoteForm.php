<?php
namespace api\models\vote\forms;

use api\models\vote\Vote;

/**
 * Class VoteForm
 * @package api\models\vote\forms
 */

/**
 * @OA\RequestBody(
 *     request="Vote",
 *     required=true,
 *     description="Данные лайка",
 *     @OA\JsonContent(
 *         @OA\Property(property="value", type="integer", description="Значение"),
 *         example={
 *             "value": 0
 *         }
 *     )
 * )
 */
class VoteForm extends Vote
{

}