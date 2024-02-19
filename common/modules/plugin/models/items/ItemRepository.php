<?php
namespace common\modules\plugin\models\items;

use yii\base\Model;

/**
 * @property string $name
 */

class ItemRepository extends Model
{
	/** @var integer */
	public $name;
	
	/** @var string */
	public $owner;
}