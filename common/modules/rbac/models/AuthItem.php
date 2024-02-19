<?php

namespace common\modules\rbac\models;

use Yii;
use yii\base\Model;

use common\modules\base\components\Debug;
use common\modules\rbac\helpers\enum\Type;
use common\modules\rbac\models\search\SearchParent;
use common\modules\rbac\models\search\SearchChild;
use common\modules\rbac\components\Item;
use common\modules\rbac\validators\RbacValidator;

abstract class AuthItem extends Model
{
	/**
	 * @var string
	 */
	public $parent;

	/**
	 * @var string
	 */
    public $name;

	/**
	 * @var integer
	 */
	public $type;

    /**
	 * @var string
	 */
    public $description;

    /**
	 * @var string
	 */
    public $rule;

    /**
	 * @var string[]
	 */
    public $children = [];

    /**
	 * @var \yii\rbac\Role|\yii\rbac\Permission
	 */
    public $item;

    /**
	 * @var \common\modules\rbac\components\DbManager
	 */
    protected $manager;

    /**
	 * @inheritdoc
	 */
    public function init() {
        parent::init();

        $this->manager = Yii::$app->authManager;

        if ($this->item instanceof Item) {
            $this->name = $this->item->name;
			$this->type = $this->item->type;
            $this->description = $this->item->description;
            $this->children = array_keys($this->manager->getChildren($this->item->name));

            if ($this->item->ruleName !== null)
                $this->rule = get_class($this->manager->getRule($this->item->ruleName));
        }
    }

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		return [
			'create' => ['name', 'description', 'children', 'rule'],
			'update' => ['name', 'description', 'children', 'rule'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['name', 'required'],
			['name', 'match', 'pattern' => '/^[\w.-]+$/'],
			[['name', 'description', 'rule'], 'trim'],
			['name', function () {
				if ($this->manager->getItem($this->name) !== null) {
					$this->addError('name', \Yii::t('rbac', 'error_auth_item_with_such_name_already_exists'));
				}
			}, 'when' => function () {
				return $this->scenario == 'create' || $this->item->name != $this->name;
			}],
			['children', RbacValidator::className()],
			['rule', function () {
				try {
					$class = new \ReflectionClass($this->rule);
				} catch (\Exception $ex) {
					$this->addError('rule', \Yii::t('rbac', 'error_class_does_not_exist', $this->rule));
					return;
				}

				if ($class->isInstantiable() == false) {
					$this->addError('rule', \Yii::t('rbac', 'error_rule_class_can_not_be_instantiated'));
				}
				if ($class->isSubclassOf('\yii\rbac\Rule') == false) {
					$this->addError('rule', \Yii::t('rbac', 'error_rule_class_must_extend'));
				}
			}],
		];
	}

    /**
	 * @inheritdoc
	 */
    public function attributeLabels() {
        return [
            'name' => Yii::t('rbac', 'field_name'),
            'description' => Yii::t('rbac', 'field_description'),
            'children' => Yii::t('rbac', 'field_children'),
            'rule' => Yii::t('rbac', 'field_rule'),
        ];
    }

    /**
     * Saves item.
     *
     * @return bool
     */
    public function save() {
        if ($this->validate() == false)
            return false;

        if ($isNewItem = ($this->item === null)) {
            $this->item = $this->createItem($this->name);
        }
		else {
            $oldName = $this->item->name;
        }

        $this->item->name = $this->name;
        $this->item->description = $this->description;

        if (!empty($this->rule)) {
            $rule = Yii::createObject($this->rule);
            if ($this->manager->getRule($rule->name) === null)
                $this->manager->add($rule);
            $this->item->ruleName = $rule->name;
        }
		else {
            $this->item->ruleName = null;
        }

        if ($isNewItem) {
            Yii::$app->session->setFlash('success', Yii::t('rbac-'.$this->getTypeName(), 'message_flash_created_success'));
            $this->manager->add($this->item);
        }
		else {
            Yii::$app->session->setFlash('success', Yii::t('rbac-'.$this->getTypeName(), 'message_flash_updated_success'));
            $this->manager->update($oldName, $this->item);
        }

        $this->updateChildren();

        return true;
    }

	/**
	 * Revoke items
	 * @param array $items
	 */
	public function revoke($items) {

		if (isset($items['parent'])) {
			Yii::$app->session->setFlash('success', Yii::t('rbac-'.$this->getTypeName(), 'message_flash_revoke_success'));
			$this->manager->removeChild($items['parent'], $this->item);
		}

		if (isset($items['child'])) {
			Yii::$app->session->setFlash('success', Yii::t('rbac-'.$this->getTypeName(), 'message_flash_revoke_success'));
			$this->manager->removeChild($this->item, $items['child']);
		}
	}

    /**
     * Updated items children.
     */
    protected function updateChildren() {
        $children = $this->manager->getChildren($this->item->name);
        $childrenNames = array_keys($children);

        if (is_array($this->children)) {

            // remove children that
            foreach (array_diff($childrenNames, $this->children) as $item)
                $this->manager->removeChild($this->item, $children[$item]);

            // add new children
            foreach (array_diff($this->children, $childrenNames) as $item)
                $this->manager->addChild($this->item, $this->manager->getItem($item));
        }
		else {
            $this->manager->removeChildren($this->item);
        }
    }

	/**
	 * Get type name
	 * @return string
	 */
	public function getTypeName() {
		$type = 'permission';
		if ($this->item->type == Item::TYPE_ROLE)
			$type = 'role';
		else if ($this->item->type == Item::TYPE_TASK)
			$type = 'task';
		return $type;
	}

	/**
	 * Get type name
	 * @return string
	 */
	public function getTypeTitle() {
		return Yii::t('rbac', 'type_'.$this->getTypeName());
	}

	// Get tree parents
	public function getTreeParents() {
		return $this->getTree(true, false);
	}

	/**
	 * Get tree childs
	 * @return array
	 */
	public function getTreeChilds() {
		return $this->getTree(false, true);
	}

	/**
	 * Get tree
	 * @return array
	 */
	public function getTree($excludeParents = false, $excludeChilds = false) {
		$tmp = [];

		// Get childs
		$exclude = [
			$this->item->name,
		];

		// Exclude parents
		if ($excludeParents) {
			$searchModel = new SearchParent($this->item->name);
			$models = $searchModel->search()->allModels;
			if ($models) {
				foreach ($models as $item)
					$exclude[] = $item['name'];
			}
		}

		// Exclude childs
		if ($excludeChilds) {
			$searchModel = new SearchChild($this->item->name);
			$models = $searchModel->search()->allModels;
			if ($models) {
				foreach ($models as $item)
					$exclude[] = $item['name'];
			}
		}

		// Get roles
		$roles = $this->manager->getItems(Item::TYPE_ROLE, $exclude);
		if ($roles) {
			foreach ($roles as $item)
				$tmp[Yii::t('rbac', 'type_roles')][$item->name] = empty($item->description) ? $item->name : $item->name.' ('.$item->description.')';
		}

		// Get tasks
		$tasks = $this->manager->getItems(Item::TYPE_TASK, $exclude);
		if ($tasks) {
			foreach ($tasks as $item)
				$tmp[Yii::t('rbac', 'type_tasks')][$item->name] = empty($item->description) ? $item->name : $item->name.' ('.$item->description.')';
		}

		// Get permissions
		$permissions = $this->manager->getItems(Item::TYPE_PERMISSION, $exclude);
		if ($permissions) {
			foreach ($permissions as $item)
				$tmp[Yii::t('rbac', 'type_permissions')][$item->parent][$item->name] = empty($item->description) ? $item->name : $item->name.' ('.$item->description.')';
		}

		return $tmp;
	}

    /**
     * @return array An array of unassigned items.
     */
    abstract public function getUnassignedItems();

    /**
     * @param string $name
     * @return \yii\rbac\Item
     */
    abstract protected function createItem($name);
}