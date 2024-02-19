<?php
namespace common\modules\tag\models\query;

use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveQuery;

use common\modules\vote\behaviors\VoteQueryBehavior;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

/**
 * This is the ActiveQuery class for [[\common\modules\tag\models\Tag]].
 *
 * @see \common\modules\tag\models\Tag
 */
class TagQuery extends ActiveQuery
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
