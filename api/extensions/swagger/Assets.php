<?php
namespace api\extensions\swagger;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class Assets
 * @package api\extenstions
 */
class Assets extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower/swagger-ui/dist';

    /**
     * @var array
     */
    public $js = [
        'swagger-ui-bundle.js',
        'swagger-ui-standalone-preset.js',
    ];

    /**
     * @var array
     */
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    /**
     * @var array
     */
    public $css = [
        [
            'swagger-ui.css',
            'media' => 'screen, print',
        ],
    ];
}
