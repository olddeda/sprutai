<?php

namespace common\modules\rbac\components;

use yii\rbac\Item as BaseItem;

class Item extends BaseItem
{
	const TYPE_ROLE = 1;
	const TYPE_PERMISSION = 2;
	const TYPE_TASK	= 3;

	/**
	 * @var string
	 */
	public $parent;
}