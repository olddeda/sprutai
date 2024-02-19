<?php
namespace common\modules\comments\models\query;

use common\modules\base\components\ArrayHelper;

use common\modules\base\components\ActiveQuery;

use common\modules\vote\behaviors\VoteQueryBehavior;

/**
 * Class CommentQuery
 * @package common\modules\comments\models\query
 */
class CommentQuery extends ActiveQuery
{
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
