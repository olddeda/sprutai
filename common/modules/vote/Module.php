<?php

namespace common\modules\vote;

use Yii;
use yii\base\InvalidConfigException;

/**
 * @package common\modules\vote
 */
class Module extends \yii\base\Module
{
    const TYPE_VOTING = 'voting';
    const TYPE_TOGGLE = 'toggle';
    const TYPE_RATING = 'rating';

    /**
     * @var bool Apply default styles by default
     */
    public $registerAsset = true;

    /**
     * @var string
     */
    public $controllerNamespace = 'common\modules\vote\controllers';

    /**
     * @var array Entities that will be used by vote widgets.
     * - `$modelName`: model class name
     * - `$allowGuests`: allow users to vote
     * - `$type`: vote type (Module::TYPE_VOTING or Module::TYPE_TOGGLE)
     */
    public $entities;

    /**
     * @var int
     */
    public $guestTimeLimit = 3600; // 1 hour per vote for guests

    /**
     * @var string
     */
    public $redirectUrl = '/site/login';

    /**
     * @param $entity
     * @return int
     */
    public function encodeEntity($entity) {
        return sprintf("%u", crc32($entity));
    }

    /**
     * @param $entity
     * @return array|null
     * @throws InvalidConfigException
     */
    public function getEntityForEncoded($entity)  {
        foreach (array_keys($this->entities) as $e) {
            if ($this->encodeEntity($e) == $entity) {
                return $this->entities[$e];
            }
        }
        return null;
    }

    /**
     * @param $entity
     * @return array|null
     * @throws InvalidConfigException
     */
    public function getSettingsForEntity($entity)  {
        if (!isset($this->entities[$entity])) {
            return null;
        }
        $settings = $this->entities[$entity];
        if (!is_array($settings)) {
            $settings = ['modelName' => $settings];
        }
        $settings = array_merge($this->getDefaultSettings(), $settings);
        if (!in_array($settings['type'], [self::TYPE_TOGGLE, self::TYPE_VOTING, self::TYPE_RATING])) {
            throw new InvalidConfigException('Unsupported voting type.');
        }

        return $settings;
    }
	
	/**
	 * @param $class
	 *
	 * @return array
	 */
    public function getEntitiesForClass($class) {
    	$tmp = [];
    	foreach ($this->entities as $entity => $params) {
    		if (isset($params['modelName']) && $params['modelName'] == $class) {
    			$tmp[] = $entity;
			}
		}
		return $tmp;
	}

    /**
     * @return array
     */
    protected function getDefaultSettings() {
        return [
            'type' => self::TYPE_VOTING,
            'allowGuests' => false,
            'allowSelfVote' => false,
            'entityAuthorAttribute' => 'user_id',
			'action' => 'vote',
        ];
    }
}
