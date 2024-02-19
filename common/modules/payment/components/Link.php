<?php
namespace common\modules\payment\components;

use yii\base\BaseObject;

/**
 * Class Link
 * @package common\modules\payment\components
 */
class Link extends BaseObject
{
    /**
     * @var string
     */
    public $protocol;
    
    /**
     * @var string
     */
    public $source;
    
    /**
     * @var null|string|string[]
     */
    public $domain;
    
    /**
     * @var
     */
    public $host;
    
    /**
     * @var
     */
    public $path;
    
    /**
     * @var array
     */
    public $parameters = [];
    
    /**
     * @var
     */
    public $hash;
    
    /**
     * @var array
     */
    public $hashParameters = [];
    
    /**
     * Link constructor.
     * @param string $config
     */
    public function __construct($config = ''){
        if (is_string($config)) {
            preg_match('/((https?:\/\/www\.)|(https?:\/\/)|(www\.))([^\/\n\r\t\"\' ]+)([^\?\n\r\t\"\'\# ]*)\??([^\n\r\t\"\'\# ]*)\#?([^\n\r\t\"\' ]*)/iu', $config, $match);

            if (count($match) > 5) {
                $this->protocol = strpos($match[1], 'https') !== false ? 'https' : 'http';
                $this->source = $config;
                $this->domain = preg_replace('/^(.+\.)?([^\.]+\.[^\.]+)$/iu', '\\2', $match[5]);
                $this->host = $match[5];
                $this->path = $match[6];
                $this->parameters = $this->stringToParameters($match[7]);
                $this->hash = $match[8];
                $this->hashParameters = $this->stringToParameters($match[8]);
            }

            $config = [];
        }

        parent::__construct($config);
    }
    
    /**
     * Check exists param
     * @param $name
     *
     * @return bool
     */
    public function hasParam($name){
        return array_key_exists($name, $this->parameters) !== false;
    }
    
    /**
     * Get param
     * @param $name
     *
     * @return mixed|null
     */
    public function getParam($name){
        return $this->hasParam($name) ? $this->parameters[$name] : null;
    }
    
    /**
     * Set param
     * @param $name
     * @param $value
     */
    public function setParam($name, $value){
        $this->parameters[$name] = $value;
    }
    
    /**
     * Set params
     * @param array $params
     */
    public function setParams(array $params){
        $this->parameters = array_merge($this->parameters, $params);
    }
    
    /**
     * Check exists hash param
     * @param $name
     *
     * @return bool
     */
    public function hasHashParam($name){
        return array_key_exists($name, $this->hashParameters);
    }
    
    /**
     * Get hash param
     * @param $name
     *
     * @return mixed|null
     */
    public function getHashParam($name){
        return $this->hasHashParam($name) ? $this->hashParameters[$name] : null;
    }
    
    /**
     * Set hash param
     * @param $name
     * @param $value
     */
    public function setHashParam($name, $value){
        $this->hashParameters[$name] = $value;
    }
    
    /**
     * Set hash params
     * @param array $params
     */
    public function setHashParams(array $params){
        $this->hashParameters = array_merge($this->hashParameters, $params);
    }
    
    /**
     * @return string
     */
    public function __toString(){
        $link = $this->protocol . '://';
        $link .= $this->host;
        $link .= $this->path;

        $stringParameters = $this->parametersToString($this->parameters);
        if ($stringParameters) {
            $link .= '?' . $stringParameters;
        }

        return $link;
    }
    
    /**
     * Convert string to params
     * @param $parametersString
     *
     * @return array
     */
    private function stringToParameters($parametersString){
        $parameters = array();
        foreach (explode('&', $parametersString) as $paramString) {
            $paramArr = explode('=', $paramString);
            if (count($paramArr) === 2) {
                $parameters[$paramArr[0]] = $paramArr[1];
            }
        }
        return $parameters;
    }
    
    /**
     * Convert params to string
     * @param $parameters
     *
     * @return string
     */
    private function parametersToString($parameters){
        return http_build_query($parameters);
    }
}