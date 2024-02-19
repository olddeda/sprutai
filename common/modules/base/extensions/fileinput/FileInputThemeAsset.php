<?php
namespace common\modules\base\extensions\fileinput;

use common\modules\base\extensions\base\AssetBundle;

/**
 * Theme Asset bundle for the FileInput Widget
 */
class FileInputThemeAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/kartik-v/bootstrap-fileinput';

    /**
     * Add file input theme file
     *
     * @param string $theme the theme file name
     */
    public function addTheme($theme)
    {
        $file = YII_DEBUG ? "theme.js" : "theme.min.js";
        if ($this->checkExists("themes/{$theme}/{$file}")) {
            $this->js[] = "themes/{$theme}/{$file}";
        } 
        $file = YII_DEBUG ? "theme.css" : "theme.min.css";
        if ($this->checkExists("themes/{$theme}/{$file}")) {
            $this->css[] = "themes/{$theme}/{$file}";
        } 
        return $this;
    }
	
	/**
	 * @inheritdoc
	 */
	public $depends = [
		'common\modules\base\extensions\fileinput\FileInputAsset'
	];
    
    /**
     * Check if file exists in path provided
     *
     * @param string $path the file path
     *
     * @return boolean
     */
    protected  function checkExists($path) {
        return file_exists(\Yii::getAlias($this->sourcePath . '/' . $path));
    }
}
