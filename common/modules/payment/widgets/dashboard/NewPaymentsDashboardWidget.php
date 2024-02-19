<?php
namespace common\modules\payment\widgets\dashboard;

use Yii;
use yii\caching\DbDependency;

use common\modules\dashboard\widgets\DashboardStatisticsWidget;

use common\modules\payment\models\Payment;
use common\modules\payment\helpers\enum\Status;

class NewPaymentsDashboardWidget extends DashboardStatisticsWidget {
	
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'new-payments';
	}
	
	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return Yii::t('payment-dashboard', 'new-payments');
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultX() {
		return 8;
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
		$this->_collect(Status::PAID);
		$this->_collect(Status::FAILED);
		//$this->_collect(Status::WAIT);
		
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
	
	private function _collect($status) {
		$tmp = [];
		
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(created_at) FROM '.Payment::tableName();
		
		$min = $this->getTimestampMin();
		$max = $this->getTimestampMax();
		
		$result = Yii::$app->db->cache(function ($db) use ($min, $max, $status){
			return $db->createCommand("
				SELECT SUM(price) AS value, created_at,  DATE_FORMAT(FROM_UNIXTIME(created_at), '".$this->getFormatQuery()."') AS d
				FROM ".Payment::tableName()."
				WHERE created_at BETWEEN :min AND :max
				AND status = :status
				GROUP BY DATE_FORMAT(FROM_UNIXTIME(created_at), '".$this->getFormatQuery()."')
				ORDER BY created_at ASC
			", [
				':min' => $min,
				':max' => $max,
				':status' => $status
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
		$this->data[$status] = $tmp;
	}
}