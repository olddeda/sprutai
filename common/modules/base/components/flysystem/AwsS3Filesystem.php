<?php
namespace common\modules\base\components\flysystem;

use creocoder\flysystem\AwsS3Filesystem AS BaseAwsS3Filesystem;

/**
 * Class AwsS3Filesystem
 * @package common\modules\base\components\flysystem
 */
class AwsS3Filesystem extends BaseAwsS3Filesystem
{
	public $url;
}