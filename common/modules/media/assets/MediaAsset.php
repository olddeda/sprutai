<?php
namespace common\modules\media\assets;

use Yii;
use yii\web\AssetBundle;

class MediaAsset extends AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = '@media/web/assets';
	
	/**
	 * @var array
	 */
	public $js = [
	];
	
	/**
	 * @var array
	 */
	public $css = [
		'css/media.css',
	];
	
	/**
	 * @var array
	 */
	public $depends = [
	];
	
}