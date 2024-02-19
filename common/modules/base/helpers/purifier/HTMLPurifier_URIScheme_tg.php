<?php

namespace common\modules\base\helpers\purifier;

/**
 * Validates tg
 */
class HTMLPurifier_URIScheme_tg extends \HTMLPurifier_URIScheme
{
    /**
     * @type bool
     */
    public $browsable = false;
    
    /**
     * @type bool
     */
    public $may_omit_host = true;
    
    /**
     * @param HTMLPurifier_URI $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function doValidate(&$uri, $config, $context)
    {
        return true;
    }
}