<?php
namespace common\modules\payment\components;

use yii\base\BaseObject;

/**
 * Class Request
 * @package common\modules\payment\components
 */
class Request extends BaseObject
{
    /**
     * @var string
     */
    public $url;
    
    /**
     * @var string
     */
    public $method = 'get';
    
    /**
     * @var array
     */
    public $params = [];

    /**
     * Check has params
     * @param array|string $params
     * @return bool
     */
    public function hasParams($params) {
        if (!is_array($params))
            $params = [$params];

        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @return string
     */
    public function __toString(){
        $link = new Link($this->url);
        if ($this->method === 'get')
            $link->parameters = $this->params;
        return (string)$link;
    }

}