<?php
namespace common\modules\base\components\jira;

use Yii;
use yii\base\Model;

/**
 * Class StatusCategory
 * @package common\modules\base\components\jira
 */
class StatusCategory extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $colorName;

    /**
     * Rest url to the status
     * @var string
     */
    public $self;
	
	/**
	 * @var array
	 */
	private static $statusCategories = [];

    /**
     * @var StatusCategory
     */
    protected $_statusCategory;
	
	/**
	 * @param $data
	 *
	 * @return StatusCategory|mixed|null
	 */
    public static function get($data) {
        if (!isset($data['id'])) {
            return null;
        }

        $id = $data['id'];
        if (array_key_exists($id, self::$statusCategories)) {
            $statusCategory = self::$statusCategories[$id];
        }
        else {
            $statusCategory = self::populate($data);
            self::$statusCategories[$statusCategory->id] = $statusCategory;
        }

        return $statusCategory;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function populate($data) {
        if (!is_array($data) || !isset($data['id'])) {
            return null;
        }
        
        $statusCategory = new self;
        $statusCategory->id = $data['id'];
        $statusCategory->key = $data['key'];
        $statusCategory->name = $data['name'];
        $statusCategory->colorName = $data['colorName'];
        $statusCategory->self = $data['self'];
        return $statusCategory;
    }
	
	/**
	 * @param $data
	 *
	 * @return array
	 */
    public static function populateAll($data) {
        $issueTypes = [];
        foreach ($data as $row) {
            $issueTypes[$row['key']] = self::populate($row);
        }
        return $issueTypes;
    }
}
