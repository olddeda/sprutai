<?php
namespace common\modules\base\components;

use Yii;
use yii\db\ActiveQueryTrait;
use yii\db\ActiveRelationTrait;
use yii\web\Application;
use yii\db\Query;

use common\modules\base\helpers\enum\Status;

class ActiveQuery extends \yii\db\ActiveQuery
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		if ($this->modelClass) {
			$model = new $this->modelClass;
			
			// Add status condition
			if (in_array('status', $model->attributes())) {
				$this->andWhere(['not in', $model::tableName().'.status', [Status::DELETED]]);
			}
		}
	}

	/**
	 * Get table name
	 * @return mixed
	 */
	public function getTableName() {
	    return call_user_func([$this->modelClass, 'tableName']);
    }
	
	/**
	 * @return $this
	 */
	public function active() {
		$this->andWhere(['status' => Status::ENABLED]);
		return $this;
	}
	
	/**
	 * @return $this
	 */
	public function deleted() {
		$this->andWhere(['status' => Status::DELETED]);
		return $this;
	}

    /**
     * {@inheritdoc}
     */
    public function prepare($builder)
    {
        if (!empty($this->joinWith)) {
            $this->buildJoinWith();
            $this->joinWith = null;    // clean it up to avoid issue https://github.com/yiisoft/yii2/issues/2687
        }

        return parent::prepare($builder);
    }

    public function buildJoinWith()
    {
        $join = $this->join;
        $this->join = [];

        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $model = $modelClass::instance();
        foreach ($this->joinWith as [$with, $eagerLoading, $joinType]) {
            $this->joinWithRelations($model, $with, $joinType);

            if (is_array($eagerLoading)) {
                foreach ($with as $name => $callback) {
                    if (is_int($name)) {
                        if (!in_array($callback, $eagerLoading, true)) {
                            unset($with[$name]);
                        }
                    } elseif (!in_array($name, $eagerLoading, true)) {
                        unset($with[$name]);
                    }
                }
            } elseif (!$eagerLoading) {
                $with = [];
            }

            $this->with($with);
        }

        // remove duplicated joins added by joinWithRelations that may be added
        // e.g. when joining a relation and a via relation at the same time
        $uniqueJoins = [];
        foreach ($this->join as $j) {
            $uniqueJoins[serialize($j)] = $j;
        }
        $this->join = array_values($uniqueJoins);

        if (!empty($join)) {
            // append explicit join to joinWith()
            // https://github.com/yiisoft/yii2/issues/2880
            $this->join = empty($this->join) ? $join : array_merge($this->join, $join);
        }
    }

    /**
     * Modifies the current query by adding join fragments based on the given relations.
     * @param ActiveRecord $model the primary model
     * @param array $with the relations to be joined
     * @param string|array $joinType the join type
     */
    public function joinWithRelations($model, $with, $joinType)
    {
        $relations = [];

        foreach ($with as $name => $callback) {
            if (is_int($name)) {
                $name = $callback;
                $callback = null;
            }

            $primaryModel = $model;
            $parent = $this;
            $prefix = '';
            while (($pos = strpos($name, '.')) !== false) {
                $childName = substr($name, $pos + 1);
                $name = substr($name, 0, $pos);
                $fullName = $prefix === '' ? $name : "$prefix.$name";
                if (!isset($relations[$fullName])) {
                    $relations[$fullName] = $relation = $primaryModel->getRelation($name);
                    $this->joinWithRelation($parent, $relation, $this->getJoinType($joinType, $fullName));
                } else {
                    $relation = $relations[$fullName];
                }
                /* @var $relationModelClass ActiveRecordInterface */
                $relationModelClass = $relation->modelClass;
                $primaryModel = $relationModelClass::instance();
                $parent = $relation;
                $prefix = $fullName;
                $name = $childName;
            }

            $fullName = $prefix === '' ? $name : "$prefix.$name";
            if (!isset($relations[$fullName])) {
                $relations[$fullName] = $relation = $primaryModel->getRelation($name);
                if ($callback !== null) {
                    call_user_func($callback, $relation);
                }
                if (!empty($relation->joinWith)) {
                    $relation->buildJoinWith();
                }
                $this->joinWithRelation($parent, $relation, $this->getJoinType($joinType, $fullName));
            }
        }
    }

    /**
     * Returns the join type based on the given join type parameter and the relation name.
     * @param string|array $joinType the given join type(s)
     * @param string $name relation name
     * @return string the real join type
     */
    private function getJoinType($joinType, $name)
    {
        if (is_array($joinType) && isset($joinType[$name])) {
            return $joinType[$name];
        }

        return is_string($joinType) ? $joinType : 'INNER JOIN';
    }

    /**
     * Joins a parent query with a child query.
     * The current query object will be modified accordingly.
     * @param ActiveQuery $parent
     * @param ActiveQuery $child
     * @param string $joinType
     */
    private function joinWithRelation($parent, $child, $joinType)
    {
        $via = $child->via;
        $child->via = null;
        if ($via instanceof self) {
            // via table
            $this->joinWithRelation($parent, $via, $joinType);
            $this->joinWithRelation($via, $child, $joinType);
            return;
        } elseif (is_array($via)) {
            // via relation
            $this->joinWithRelation($parent, $via[1], $joinType);
            $this->joinWithRelation($via[1], $child, $joinType);
            return;
        }

        [$parentTable, $parentAlias] = $parent->getTableNameAndAlias();
        [$childTable, $childAlias] = $child->getTableNameAndAlias();

        if (!empty($child->link)) {
            if (strpos($parentAlias, '{{') === false) {
                $parentAlias = '{{' . $parentAlias . '}}';
            }
            if (strpos($childAlias, '{{') === false) {
                $childAlias = '{{' . $childAlias . '}}';
            }

            $on = [];
            foreach ($child->link as $childColumn => $parentColumn) {
                $on[] = "$parentAlias.[[$parentColumn]] = $childAlias.[[$childColumn]]";
            }
            $on = implode(' AND ', $on);
            if (!empty($child->on)) {
                $on = ['and', $on, $child->on];
            }
        } else {
            $on = $child->on;
        }

        if (is_array($child->from) && is_array($child->on) && count($child->from) && count($child->on)) {
            $alias = str_replace('{', '', $childAlias);
            $alias = str_replace('}', '', $alias);
            if (isset($child->from[$alias])) {
                foreach ($on as $key => $val) {
                    if (is_array($val) && $val == $child->on) {
                        $childOn = [];
                        foreach ($val as $key2 => $val2) {
                            if (strpos($key2, '{{') === false && strpos($key2, $alias.'.') === false && !is_int($key2)) {
                                $key2 = "$childAlias.[[$key2]]";
                                $childOn[$key2] = $val2;
                            }
                        }
                        if (count($childOn)) {
                            $on[$key] = $childOn;
                        }
                    }
                }
            }
        }

        $this->join($joinType, empty($child->from) ? $childTable : $child->from, $on);

        if (!empty($child->where)) {
            $this->andWhere($child->where);
        }
        if (!empty($child->having)) {
            $this->andHaving($child->having);
        }
        if (!empty($child->orderBy)) {
            $this->addOrderBy($child->orderBy);
        }
        if (!empty($child->groupBy)) {
            $this->addGroupBy($child->groupBy);
        }
        if (!empty($child->params)) {
            $this->addParams($child->params);
        }
        if (!empty($child->join)) {
            foreach ($child->join as $join) {
                $this->join[] = $join;
            }
        }
        if (!empty($child->union)) {
            foreach ($child->union as $union) {
                $this->union[] = $union;
            }
        }
    }
}