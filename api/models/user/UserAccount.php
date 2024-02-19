<?php
namespace api\models\user;

use common\modules\user\models\UserAccount as BaseUserAccount;

use yii\db\ActiveQuery;

/**
 * Class UserAccount
 * @package api\models\user
 */
class UserAccount extends BaseUserAccount
{
    /**
     * @return ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}