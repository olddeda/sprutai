<?php
namespace common\modules\plugin\models\items;

use yii\base\Model;

/**
 * @property string $tag
 * @property string $description
 * @property string $reference
 * @property int $created_at
 * @property int $published_at
 */

class ItemRelease extends Model
{
	/** @var string */
	public $tag;
	
	/** @var string */
	public $description;
	
	/** @var string */
	public $reference;
	
	/** @var integer */
	public $created_at;
	
	/** @var integer */
	public $published_at;
}