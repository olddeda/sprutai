<?php
namespace common\modules\content\widgets\dashboard;

use Yii;
use yii\caching\DbDependency;

use common\modules\dashboard\widgets\DashboardStatisticsWidget;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Type;

class NewContentDashboardWidget extends DashboardStatisticsWidget {
	
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'new-content';
	}
	
	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return Yii::t('content-dashboard', 'new-content');
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultX() {
		return 4;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultY() {
		return 5;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultWidth() {
		return 4;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultHeight() {
		return 6;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMinWidth() {
		return 4;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMinHeight() {
		return 6;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMaxHeight() {
		return 6;
	}
	
	/** @var array  */
	private $data = [];
	
	/** @var array  */
	private $values = [];
	
	/**
	 * @inheritdoc
	 */
	public function render($view, $params = []) {
		$this->_collect(Type::ARTICLE);
		$this->_collect(Type::NEWS);
		$this->_collect(Type::PROJECT);
		
		$info = [
			'min' => count($this->values) ? min($this->values) : 0,
			'max' => count($this->values) ? max($this->values) : 0,
		];
		
		return parent::render($this->getName(), [
			'data' => $this->data,
			'period' => $this->getPeriod(),
			'format' => $this->getFormat(),
			'min' => $info['min'],
			'max' => $info['max'],
		]);
	}
	
	private function _collect($type) {
		$tmp = [];
		
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(created_at) FROM '.Content::tableName();
		
		$min = $this->getTimestampMin();
		$max = $this->getTimestampMax();
		
		$result = Yii::$app->db->cache(function ($db) use ($min, $max, $type){
			return $db->createCommand("
				SELECT COUNT(created_at) AS value, created_at,  DATE_FORMAT(FROM_UNIXTIME(created_at), '".$this->getFormatQuery()."') AS d
				FROM ".Content::tableName()."
				WHERE created_at BETWEEN :min AND :max
				AND type = :type
				GROUP BY DATE_FORMAT(FROM_UNIXTIME(created_at), '".$this->getFormatQuery()."')
				ORDER BY created_at ASC
			", [
				':min' => $min,
				':max' => $max,
				':type' => $type,
			])->queryAll();
		}, Yii::$app->params['cache.duration'], $dependency);
		
		if ($result) {
			$tmpDates = [];
			foreach ($result as $row) {
				$tmpDates[$row['d']] = $row['value'];
				$this->values[] = $row['value'];
			}
			foreach ($this->getDates() as $dKey => $dVal) {
				$val = isset($tmpDates[$dKey]) ? $tmpDates[$dKey] : 0;
				$tmp[] = [$dVal['timestamp'] * 1000, $val];
			}
		}
		$this->data[$type] = $tmp;
	}
	
	
}