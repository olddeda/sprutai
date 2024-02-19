<?php

namespace client\assets;

use Yii;
use yii\web\AssetBundle;

class StorageApiAsset extends AssetBundle
{
	public $sourcePath = '@bower/jquery-storage-api';
	public $js = [
		'jquery.storageapi.js',
	];
}
