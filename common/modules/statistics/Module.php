<?php
namespace common\modules\statistics;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
	/** @var string name of the component to use for database access */
	public $db = 'db';
	
	/** @var int  */
	public $timeInterval = 86400;
	
	/**
	 * @return \yii\db\Connection the database connection.
	 */
	public function getDb() {
		return Yii::$app->{$this->db};
	}
}
