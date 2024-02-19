<?php
namespace common\modules\seo\behaviors;

use yii\base\Behavior;

/**
 * Class SeoBehavior
 * @package common\modules\seo\behaviors
 *
 * @property \common\modules\seo\models\Seo $seo
 */
class SeoBehavior extends Behavior
{
	public $seo;
	
	/** @var  */
	public $h1;
	
	/** @var string */
	public $keywords;
	
	/** @var string */
	public $description;
}