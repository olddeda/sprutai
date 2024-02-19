<?php
namespace common\modules\base\assets;

use yii\web\AssetBundle;

class CodeMirrorAsset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@common/modules/base/assets/codemirror';
	
	/**
	 * @var array
	 */
    public $js = [
    	
    	// General
        'lib/codemirror.js',
	    
	    // Addons
	    'addon/formatting.js',
	    'addon/edit/closetag.js',
	    'addon/fold/xml-fold.js',
	    'addon/edit/matchbrackets.js',
	    'addon/runmode/runmode.js',
	    'addon/lint/jshint.js',
	    'addon/lint/jsonlint.js',
	    'addon/lint/lint.js',
	    'addon/lint/javascript-lint.js',
	    'addon/lint/json-lint.js',
	    
	    // Mode
	    'mode/shell/shell.js',
	    'mode/javascript/javascript.js',
		'mode/yaml/yaml.js',
	    'mode/http/http.js',
	    'mode/xml/xml.js',
	    'mode/sql/sql.js',
	    'mode/ruby/ruby.js',
	    'mode/python/python.js',
	    'mode/php/php.js',
	    'mode/perl/perl.js',
	    'mode/markdown/markdown.js',
	    'mode/lua/lua.js',
	    'mode/css/css.js',
	    'mode/htmlmixed/htmlmixed.js',
	    
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'lib/codemirror.css',
	    'theme/eclipse.css',
	    'addon/lint/lint.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
	    'common\modules\base\assets\ClipboardAsset',
    ];
}
