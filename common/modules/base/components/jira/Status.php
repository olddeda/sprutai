<?php
namespace common\modules\base\components\jira;

use Yii;
use yii\base\Model;

/**
 * Class Status
 * @package common\modules\base\components\jira
 *
 * @property StatusCategory $statusCategory
 */
class Status extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $iconUrl;

    /**
     * Rest url to the status
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $colorName;
	
	/**
	 * @var array
	 */
	private static $statuses = [];

    /**
     * @var StatusCategory
     */
    protected $_statusCategory;
	
	/**
	 * @param $data
	 *
	 * @return Status|mixed|null
	 */
    public static function get($data) {
        if (!isset($data['id'])) {
            return null;
        }
        
        $id = $data['id'];
        
        if (array_key_exists($id, self::$statuses)) {
            $status = self::$statuses[$id];
        }
        else {
            $status = self::populate($data);
            self::$statuses[$status->id] = $status;
        }
        
        return $status;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function populate($data) {
        if (!is_array($data) || !isset($data['id'])) {
            return null;
        }

        $status = new self;
        $status->id = $data['id'];
        $status->name = $data['name'];
        $status->description = $data['description'];
        $status->iconUrl = $data['iconUrl'];
        $status->self = $data['self'];
        $status->colorName = (isset($data['colorName'])) ? $data['colorName'] : false;
        $status->_statusCategory = StatusCategory::get($data['statusCategory']);

        return $status;
    }
	
	/**
	 * @param $data
	 *
	 * @return array
	 */
    public static function populateAll($data) {
        $statuses = [];
        foreach ($data as $row) {
            $statuses[$row['name']] = self::populate($row);
        }

        return $statuses;
    }
	
	/**
	 * @return StatusCategory
	 */
    public function getStatusCategory() {
        return $this->_statusCategory;
    }
}
