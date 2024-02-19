<?php
namespace common\modules\user\models\query;

use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveQuery;
use common\modules\base\components\Debug;

use common\modules\vote\behaviors\VoteQueryBehavior;

use common\modules\user\models\User;

/**
 * This is the ActiveQuery class for [[\common\modules\company\models\Company]].
 *
 * @see common\modules\user\models\query\UserQuery
 */

class UserQuery extends ActiveQuery
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		$tableName = User::tableName();
		$this->andWhere(['is', $tableName.'.deleted_at', null]);
		parent::init();
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(),[
			[
				'class' => VoteQueryBehavior::class,
			],
		]);
	}

}