<?php
namespace common\modules\base\components\jira;

use Yii;
use yii\base\Model;

/**
 * Class Priority
 * @package common\modules\base\components\jira
 */
class Priority extends Model
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
    public $iconUrl;

    /**
     * Rest url to the status
     * @var string
     */
    public $self;
	
	/**
	 * @var array
	 */
	private static $priorities = [];
	
	/**
	 * @param $data
	 *
	 * @return Priority|mixed|null
	 */
    public static function get($data) {
        if (!isset($data['id'])) {
            return null;
        }

        $id = $data['id'];

        if (array_key_exists($id, self::$priorities)) {
            $priority = self::$priorities[$id];
        } else {
            $priority = self::populate($data);
            self::$priorities[$priority->id] = $priority;
        }


        return $priority;
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
        $status->iconUrl = $data['iconUrl'];
        $status->self = $data['self'];

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

}
