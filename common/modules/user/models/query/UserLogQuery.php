<?php

namespace common\modules\user\models\query;

use yii\redis\ActiveQuery;
use common\modules\user\models\User;

/**
 * This is the ActiveQuery class
 *
 * @see common\modules\user\models\query\UserLogQuery
 */

class UserLogQuery extends ActiveQuery
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
	}

}