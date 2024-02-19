<?php
namespace common\modules\content\models\query;

use common\modules\content\models\Content;
use Yii;

use common\modules\base\components\ActiveQuery;
use common\modules\base\behaviors\tree\adjacency\AdjacencyListQueryTrait;
use common\modules\base\components\ArrayHelper;

use common\modules\base\components\Debug;
use common\modules\vote\behaviors\VoteQueryBehavior;

/**
 * This is the ActiveQuery class for [[\common\modules\content\models\Content]].
 *
 * @see \common\modules\content\models\Content
 */
class ContentQuery extends ActiveQuery
{
	use AdjacencyListQueryTrait;
	
	/**
	 * @var type
	 */
	public $type;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		$modelClass = $this->modelClass;
		$tableName = $modelClass::tableName();
		if ($this->type)
			$this->andWhere([$tableName.'.type' => $this->type]);
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
	
	/**
	 * Connect votes
	 * @return $this
	 */
	public function votes() {
		$entities = $this->getModule()->getEntitiesForClass(Content::class);
		
		foreach ($entities as $entity) {
			$this->owner->withVoteAggregate($entity);
			$this->owner->withUserVote($entity);
		}
		
		return $this->owner;
	}
}
