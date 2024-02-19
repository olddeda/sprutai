<?php
namespace api\models\user\forms;

use api\models\user\UserProfile;

/**
 *
 * @OA\RequestBody(
 *     request="UserProfileForm",
 *     required=true,
 *     description="Данные пользователя",
 *     @OA\JsonContent(
 *         @OA\Property(property="id", type="integer", description="ID"),
 *         @OA\Property(property="first_name", type="string", description="Имя"),
 *         @OA\Property(property="last_name", type="string", description="Фамилия")
 *     )
 * )
 */

/**
 * Class ContentForm
 * @package api\models\content\forms
 */
class UserProfileForm extends UserProfile {

}