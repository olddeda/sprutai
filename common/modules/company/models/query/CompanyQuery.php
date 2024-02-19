<?php
namespace common\modules\company\models\query;

use yii\helpers\ArrayHelper;

use common\modules\vote\behaviors\VoteQueryBehavior;

use common\modules\base\components\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\modules\company\models\Company]].
 *
 * @see \common\modules\company\models\Company
 */
class CompanyQuery extends ActiveQuery
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
