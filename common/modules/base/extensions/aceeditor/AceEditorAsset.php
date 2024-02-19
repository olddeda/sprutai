<?php
namespace common\modules\base\extensions\aceeditor;

use yii\web\AssetBundle;

/**
 * Class AceEditorAsset
 * @package common\modules\base\extensions\aceeditor
 */
class AceEditorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/ace-builds/src-min-noconflict';

    /**
     * @inheritdoc
     */
    public $js = [
        'ace.js',
		'ext-language_tools.js',
		'ext-modelist.js',
    ];

} 