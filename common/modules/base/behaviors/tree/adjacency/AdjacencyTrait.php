<?php
namespace common\modules\base\behaviors\tree\adjacency;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

trait AdjacencyTrait
{
	static private $_tree;

	/**
	 * Get tree list data
	 * @param string $idAttribute
	 * @param string $titleAttribute
	 * @param string $repeat
	 *
	 * @return array
	 */
	static public function tree($idAttribute = 'id', $titleAttribute = 'title', $repeat = '-') {
		$class = get_called_class();
		$classShort = (new \ReflectionClass($class))->getShortName();
		$key = 'tree_adjacency_'.strtolower($classShort);
		
		$data = Yii::$app->cache->get($key);
		
		if (!$data) {
			
			self::$_tree = [];
			
			$roots = self::find()->roots()->all();
			if ($roots)
				self::_treeListData($roots, $idAttribute, $titleAttribute, 0, $repeat);
			
			$data = self::$_tree;
			
			// Create dependency
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.$class::tableName();
			
			// Set cache
			Yii::$app->cache->set($key, $data, Yii::$app->params['cache.duration'], $dependency);
		}
		
		return $data;
	}
	
	private static function _treeListData($data, $idAttribute, $titleAttribute, $idx, $repeat) {
		foreach ($data as $item) {
			$title = $item->$titleAttribute;
			if ($repeat && $idx)
				$title = str_pad($repeat, $idx).' '.$title;
			self::$_tree[$item->$idAttribute] = $title;
			$item->populateTree();
			if ($item->children)
				self::_treeListData($item->children, $idAttribute, $titleAttribute, ++$idx, $repeat);
		}
	}
}