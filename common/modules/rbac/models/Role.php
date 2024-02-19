<?php

namespace common\modules\rbac\models;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Debug;

class Role extends AuthItem
{

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'name' => Yii::t('rbac-role', 'field_name'),
			'description' => Yii::t('rbac-role', 'field_description'),
			'rule' => Yii::t('rbac-role', 'field_rule'),
			'children' => Yii::t('rbac-role', 'field_children'),
		];
	}

    /**
	 * @inheritdoc
	 */
    public function getUnassignedItems() {

        return ArrayHelper::map($this->manager->getItems(null, $this->item !== null ? [$this->item->name] : []), 'name', function ($item) {
            return empty($item->description) ? $item->name : $item->name.' ('.$item->description.')';
        });
    }

    /**
	 * @inheritdoc
	 */
    protected function createItem($name) {
        return $this->manager->createRole($name);
    }
}