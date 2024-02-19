<?php
namespace common\modules\content\models;

/**
 * This is the model class for content owner.
 *
 * @property integer $id
 * @property string $title
 * @property string $type
 * @property string $url
 * @property bool $isCompany
 */
class ContentOwner
{
	/**
	 * @var integer
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $title;
	
	/**
	 * @var string
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var bool
	 */
	public $isCompany;
}