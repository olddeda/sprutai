<?php
namespace common\modules\lookup\models\query;

use common\modules\base\components\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\modules\lookup\models\Lookup]].
 *
 * @see \common\modules\lookup\models\Lookup
 */
class LookupQuery extends ActiveQuery
{
	/**
	 * @var integer
	 */
	public $type;
	
	/**
	 * @inheritdoc
	 */
	public function prepare($builder) {
		if ($this->type)
			$this->andWhere(['type' => $this->type]);
		return parent::prepare($builder);
	}
}
