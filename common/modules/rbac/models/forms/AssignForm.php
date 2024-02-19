<?php

namespace common\modules\rbac\models\forms;

use common\modules\rbac\helpers\enum\Type;
use Yii;
use yii\base\Model;

use common\modules\base\components\Debug;

class AssignForm extends Model
{
	const TYPE_PARENT	= 'parent';
	const TYPE_CHILD	= 'child';

	/**
	 * @var int
	 */
	public $type;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $parent;

	/**
	 * @var string
	 */
	public $child;

	/**
	 * @inheritdoc
	 */
	public function formName() {
		return 'rbac-assign-form';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'parent' => Yii::t('rbac-assign', 'field_parent'),
			'child' => Yii::t('rbac-assign', 'field_child'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name', 'type'], 'required'],
			[['parent'], 'required', 'message' => Yii::t('rbac-assign', 'error_field_parent_is_empty'), 'when' => function($data) {
				return $data->type == self::TYPE_PARENT;
			}],
			[['child'], 'required', 'message' => Yii::t('rbac-assign', 'error_field_child_is_empty'), 'when' => function($data) {
				return $data->type == self::TYPE_CHILD;
			}],
		];
	}


	/**
	 * Save assign
	 */
	public function save() {
		$auth = Yii::$app->authManager;

		// Get parent and child elements
		$parent = ($this->type == self::TYPE_CHILD) ? $auth->getItem($this->name) : $auth->getItem($this->parent);
		$child = ($this->type == self::TYPE_PARENT) ? $auth->getItem($this->name) : $auth->getItem($this->child);

		// Check and assign child
		if ($parent && $child) {
			if (!$auth->hasChild($parent, $child)) {
				Yii::$app->session->setFlash('success', Yii::t('rbac-'.Type::getItem($child->type), 'message_flash_assign_success'));
				$auth->addChild($parent, $child);

				return true;
			}
			else
				Yii::$app->session->setFlash('warning', Yii::t('rbac-assign', 'message_flash_exists'));
		}
		else {
			Yii::$app->session->setFlash('danger', Yii::t('rbac-assign', 'message_flash_error'));
		}

		return false;
	}
}