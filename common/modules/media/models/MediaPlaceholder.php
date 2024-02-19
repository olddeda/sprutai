<?php
namespace common\modules\media\models;

use Yii;

use common\modules\base\components\Debug;

class MediaPlaceholder extends MediaImage
{
	public $path;
	
	public $attribute;
	public $ext;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		if (!is_null($this->path)) {
			$realPath = Yii::getAlias($this->path);
			if (file_exists($realPath)) {
				
				$pathInfo = pathinfo($realPath);
				
				$this->status = 1;
				$this->attribute = $pathInfo['filename'];
				$this->ext = $pathInfo['extension'];
				
				return;
				
				if (!$this->module->fs->has($this->getFilePath(true).$this->getFile())) {
					$fs = $this->module->getFs();
					$filePath = $this->getFilePath(true).$this->getFile();
					$fs->write($filePath, file_get_contents($realPath));
 				}
			}
		}
	}
	
	/**
	 * Get dir
	 *
	 * @return array
	 */
	public function getDir($isOriginal = false, $key = null) {
		$p = self::getUserDir(0, 'path', $isOriginal).'placeholder'.DIRECTORY_SEPARATOR.Yii::$app->id.DIRECTORY_SEPARATOR;
		$h = '/'.self::getUserDir(0, 'http', $isOriginal).'placeholder/'.Yii::$app->id.'/';
		$u = '/'.self::getUserDir(0, 'http', $isOriginal).'placeholder/'.Yii::$app->id.'/';
		
		$tmp = [
			'path' => $p,
			'http' => $h,
			'url' => $u
		];
		
		return ($key) ? $tmp[$key] : $tmp;
	}
	
	/**
	 * Get user path
	 * @param integer $id
	 * @param string $type
	 * @param bool $isOriginal
	 *
	 * @return string
	 */
	public function getUserDir($id, $type = 'path', $isOriginal = false) {
		$sp = ($type == 'http') ? '/' : DIRECTORY_SEPARATOR;
		$ret = '';
		if ($type != 'path')
			$ret .= 'static'.$sp;
		$ret .= $this->module->fsRootPath.$sp;
		$ret .= ($isOriginal) ? $this->module->fsOriginalPath : $this->module->fsCachePath;
		$ret .= $sp;
		return $ret;
	}
}