<?php

namespace common\modules\rbac\models;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\rbac\components\Item;

class Permission extends AuthItem
{
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'name' => Yii::t('rbac-permission', 'field_name'),
			'description' => Yii::t('rbac-permission', 'field_description'),
			'rule' => Yii::t('rbac-permission', 'field_rule'),
			'children' => Yii::t('rbac-permission', 'field_children'),
		];
	}

    /**
	 * @inheritdoc
	 */
    public function getUnassignedItems() {
        return ArrayHelper::map($this->manager->getItems(Item::TYPE_PERMISSION, $this->item !== null ? [$this->item->name] : []), 'name', function ($item) {
            return empty($item->description) ? $item->name : $item->name.' ('.$item->description.')';
        });
    }

    /**
	 * @inheritdoc
	 */
    protected function createItem($name) {
        return $this->manager->createPermission($name);
    }
}