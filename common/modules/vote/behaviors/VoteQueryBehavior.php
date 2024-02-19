<?php
namespace common\modules\vote\behaviors;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use Yii;
use yii\base\Behavior;
use yii\db\Expression;

use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;
use common\modules\vote\traits\ModuleTrait;
use yii\web\Application;

/**
 * Class VoteQueryBehavior
 * @package common\modules\vote\behaviors
 * @property $own;er \yii\db\ActiveQuery
 */
class VoteQueryBehavior extends Behavior
{
    use ModuleTrait;

    /**
     * @var bool
     */
    protected $selectAdded = false;

    /**
     * Include vote aggregate model/values.
     *
     * @param $entity
     * @return \yii\base\Component
     * @throws \yii\base\InvalidConfigException
     */
    public function withVoteAggregate($entity, $userId = false) {
        $entityEncoded = $this->getModule()->encodeEntity($entity);
        $voteAggregateTable = VoteAggregate::tableName();
        $model = new $this->owner->modelClass();
        $this->initSelect($model);
        
        $on = [
			"{$entity}Aggregate.entity" => $entityEncoded,
		];
        if (!$userId) {
        	$on["{$entity}Aggregate.entity_id"] = new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`");
		}
        
        $this->owner->leftJoin("$voteAggregateTable {$entity}Aggregate", $on);

        $this->owner->addSelect([
			new Expression("`{$entity}Aggregate`.`positive` as `{$entity}Positive`"),
			new Expression("`{$entity}Aggregate`.`negative` as `{$entity}Negative`"),
			new Expression("`{$entity}Aggregate`.`rating` as `{$entity}Rating`"),
		]);

        return $this->owner;
    }

    /**
     * Include user vote status.
     *
     * @param string $entity
	 * @param integer $userId
	 *
     * @return \yii\base\Component
     * @throws \yii\base\InvalidConfigException
     */
    public function withUserVote($entity, $userId = false) {
        $entityEncoded = $this->getModule()->encodeEntity($entity);
        $model = new $this->owner->modelClass();
        $voteTable = Vote::tableName();
        $this->initSelect($model);

        $joinCondition = [
            "$entity.entity" => $entityEncoded,
        ];
        if (!$userId) {
			$joinCondition["$entity.entity_id"] = new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`");
		}

        $this->owner->addGroupBy("`{$model->tableSchema->name}`.`{$model->tableSchema->primaryKey[0]}`");
        
        if (!$userId) {
			if (Yii::$app->user->isGuest) {
				if (Yii::$app instanceof Application) {
					$joinCondition["{$entity}.user_ip"] = Yii::$app->request->userIP;
					$joinCondition["{$entity}.user_id"] = null;
				}
			}
			else {
				$joinCondition["{$entity}.user_id"] = Yii::$app->user->id;
			}
		}
		else {
			$joinCondition["{$entity}.user_id"] = new Expression("`{$model->tableSchema->name}`.`{$model->primaryKey()[0]}`");;
		}

        $this->owner->leftJoin("$voteTable $entity", $joinCondition)->addSelect([
             new Expression("`$entity`.`value` as `{$entity}UserValue`")
		]);

        return $this->owner;
    }
	
	/**
	 * Connect votes
	 * @return $this
	 */
	public function votes() {
		
		/** @var \common\modules\vote\Module $module */
		$module = Yii::$app->getModule('vote');
		
		$model = new $this->owner->modelClass;
		
		$class = get_class($model);
		while (get_parent_class($class) !== ActiveRecord::class)
			$class = get_parent_class($class);
		
		$entities = $this->getModule()->getEntitiesForClass($class);
		
		foreach ($entities as $entity) {
			$this->owner->withVoteAggregate($entity);
			$this->owner->withUserVote($entity);
		}
		
		return $this->owner;
	}
	
	/**
	 * Get voted by entity
	 *
	 * @param string $entity
	 * @param integer $value
	 * @param string $order
	 *
	 * @return $this
	 */
	public function voted($entity, $value = 1, $order = 'created_at') {
		$this->owner->andWhere([
			$entity.'.value' => $value,
			$entity.'.user_id' => Yii::$app->user->id,
		]);
		
		$this->owner->orderBy([$entity.'.'.$order => SORT_DESC]);
		
		return $this->owner;
	}
	
	/**
	 * Get subscribers by entity
	 *
	 * @param $entity
	 * @param string $order
	 *
	 * @return $this
	 */
	public function subscribers($entity, $userId, $order = 'created_at') {
		$this->owner->withVoteAggregate($entity, $userId);
		$this->owner->withUserVote($entity, $userId);
		
		$this->owner->andWhere([
			$entity.'.value' => 1,
			$entity.'.entity_id' => $userId,
		]);
		
		$this->owner->orderBy([$entity.'.'.$order => SORT_DESC]);
		
		return $this->owner;
	}

    /**
     * Add `{{%table}}`.* as first table attributes to select.
     *
     * @param $model
     */
    protected function initSelect($model) {
        if (!$this->selectAdded && (is_array($this->owner->select) && !array_search('*', $this->owner->select)) ||
            !isset($this->owner->select)) {
            $this->owner->addSelect("{$model->tableSchema->name}.*");
            $this->selectAdded = true;
        }
    }
}
