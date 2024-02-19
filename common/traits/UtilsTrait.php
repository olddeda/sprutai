<?php

namespace common\traits;

use Yii;

use FilesystemIterator;

trait UtilsTrait
{
	public static $dir = array(
		'media' => 'media',
		'auction_house' => 'auction_house',
		'auction' => 'auction',
		'auction_lot' => 'auction_lot',
		'organizer' => 'organizer',
		'specialization' => 'specialization',
		'catalog' => 'catalog',
		'event' => 'event',
		'news' => 'news',
		'avatar' => 'avatar',
		'image' => 'image',
		'video' => 'video',
		'audio' => 'audio'
	);

	/**
	 * Get image
	 * @param type $name
	 * @param type $time
	 * @return array
	 */
	protected function getImageInfo($name, $time, $http = true) {
		$image = false;
		$imageInfo = $this->imageDir;
		if (self::fileExists($imageInfo['filesys'].$name)) {
			$image['path'] = $imageInfo['url'];
			$image['file'] = $name.'?'.$time;
			if ($http)
				$image['url'] = $imageInfo['http'];
		}
		return $image;
	}

	/**
	 * Check exists file
	 * @param object $fileName
	 * @return bool
	 */
	static public function fileExists($fileName) {
		return file_exists($fileName);
	}

	/**
	 * Make dir reqursive
	 *
	 * @return
	 * @param object $dir
	 * @param object $mode[optional]
	 */
	static public function makeDirectory($dir, $mode = 0777) {
		$__oldumask = umask(0);
		$currpath = '';

		$webRoot = realpath(dirname(__FILE__).'/../../..');
		$serverRoot = explode(DIRECTORY_SEPARATOR, $webRoot.DIRECTORY_SEPARATOR.'media');

		foreach (explode(DIRECTORY_SEPARATOR, $dir) as $part) {
			$currpath .= $part.DIRECTORY_SEPARATOR;
			if (in_array($part, $serverRoot))
				continue;
			if ($part != '' && !is_dir($currpath)) {
				if (is_file($currpath))
					return null;
				if (!mkdir($currpath, $mode))
					return null;
			}
		}
		umask($__oldumask);
		return true;
	}

	/**
	 * Empty dir
	 * @param null $path
	 * @param bool|false $all
	 */
	static public function emptyDirectory($path = null, $all = false) {
		$path = pathinfo($path);
		if (!empty($path)) {
			$files = scandir($path['dirname']);
			$pattern = '/_'.$path['basename'].'$/';
			foreach ($files as $k => $v) {
				if (preg_match($pattern, $v) || $all) {
					unlink($path['dirname'].'/'.$v);
				}
			}
		}
	}

	/**
	 * Empty dir cache
	 * @param $dirname
	 * @param string $baseName
	 * @param bool|false $showLog
	 *
	 * @return bool
	 */
	static public function emptyDirectoryCache($dirname, $baseName = 'image.png', $showLog = false) {
		if (is_dir($dirname))
			$dir_handle = opendir($dirname);

		if (!$dir_handle)
			return false;

		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				$fullPath = $dirname.DIRECTORY_SEPARATOR.$file;
				if (!is_dir($fullPath)) {
					if ($file == $baseName) {
						$fi = new FilesystemIterator($dirname, FilesystemIterator::SKIP_DOTS);
						if (iterator_count($fi) > 1) {

							$isAddCount = false;
							foreach ($fi as $fileinfo) {
								if (preg_match('/_'.$baseName.'$/', $fileinfo->getFilename())) {
									$isAddCount = true;
								}
							}

							self::emptyDirectory($fullPath);
						}
					}
				}
				else
					self::emptyDirectoryCache($fullPath, $baseName);
			}
		}

		closedir($dir_handle);
		return true;
	}

	/**
	 * Get media dir
	 * @return
	 * @param object $params
	 */
	static public function getDir($param) {
		$schema = (Yii::$app->request->isSecureConnection) ? 'https' : 'http';
		$f = Yii::$app->fs->path.DIRECTORY_SEPARATOR.self::getClientDir($param['client_id']);
		$h = $schema.'://'.$_SERVER['HTTP_HOST'].'/'.self::getClientDir($param['client_id'], 'http');
		$u = '/'.self::getClientDir($param['client_id'], 'http');
		if (isset($param['path'])) {
			$f.= $param['path'].DIRECTORY_SEPARATOR;
			$h.= $param['path'].'/';
			$u.= $param['path'].'/';
		}
		if (isset($param['content_id'])) {
			$f.= $param['content_id'].DIRECTORY_SEPARATOR;
			$h.= $param['content_id'].'/';
			$u.= $param['content_id'].'/';
		}
		if (isset($param['subcontent_id'])) {
			$f.= $param['subcontent_id'].DIRECTORY_SEPARATOR;
			$h.= $param['subcontent_id'].'/';
			$u.= $param['subcontent_id'].'/';
		}
		if (isset($param['page_id'])) {
			$f.= $param['page_id'].DIRECTORY_SEPARATOR;
			$h.= $param['page_id'].'/';
			$u.= $param['page_id'].'/';
		}

		return array(
			'filesys' => $f,
			'http' => $h,
			'url' => $u
		);
	}

	/**
	 * Get client dir
	 *
	 * @param object $type[optional]
	 * @param object $id[optional]
	 */
	public static function getClientDir($id, $type = 'filesys') {
		$sp = ($type == 'http') ? '/' : DIRECTORY_SEPARATOR;
		$prefix = ($type == 'http') ? 'static'.$sp : '';
		return $prefix.self::$dir['media'].$sp.self::getSplitPath($id, $type);
	}

	/**
	 * Кусок пути сплитованный по дирректориям
	 *
	 * @return
	 * @param object $id
	 * @param object $type[optional]
	 */
	static public function getSplitPath($id, $type = 'filesys') {
		$sp = ($type == 'http') ? '/' : DIRECTORY_SEPARATOR;
		if (strlen($id) < 6)
			$id = str_repeat('0', 6 - strlen($id)).$id;
		$path = substr($id, -6);
		return $path{0}.$path{1}.$sp.$path{2}.$path{3}.$sp.$path{4}.$path{5}.$sp;
	}
}