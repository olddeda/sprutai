<?php
namespace common\modules\dashboard\widgets;

use Yii;

use DateTime;

use common\modules\base\helpers\Arr;

use common\modules\dashboard\helpers\enum\Range;

use common\modules\base\components\Debug;

class DashboardStatisticsWidget extends DashboardWidget
{
	private $_range = Range::DAY;
	
	private $_period = 'hour';
	
	private $_format = 'H:mm';
	
	private $_formatQuery = 'H:mm';
	
	private $_dates = null;
	
	public function init() {
		parent::init();
		
		if (Yii::$app->request->get('widget') == $this->getName() && !is_null(Yii::$app->request->get('range')))
			$this->setParam('range', Yii::$app->request->get('range'));
		$this->_range = $this->getParam('range', Range::DAY);
	}
	
	public function getPanelHeader() {
		return parent::render('@common/modules/dashboard/widgets/views/_header_date');
	}
	
	/**
	 * @return int
	 */
	public function getRange() {
		return $this->_range;
	}
	
	public function getPeriod() {
		return $this->_period;
	}
	
	public function getFormat() {
		return $this->_format;
	}
	
	public function getFormatQuery() {
		return $this->_formatQuery;
	}
	
	public function getDates() {
		if (is_null($this->_dates)) {
			$this->_dates = [];
			
			if ($this->_range == Range::DAY) {
				$this->_period = 'hour';
				$this->_format = 'H:mm';
				$this->_formatQuery = '%Y-%m-%d %H';
				
				$d = new DateTime();
				$this->_dates[$d->format('Y-m-d H')] = [
					'date' => $d->format('Y-m-d H'),
					'timestamp' => $d->getTimestamp(),
					'format' => '%Y-%m-%d %H',
				];
				$d->setTime($d->format('H'), 00, 00);
				
				for ($i = 0; $i < 23; $i++) {
					$d->modify('-1 hours');
					$this->_dates[$d->format('Y-m-d H')] = [
						'date' => $d->format('Y-m-d H'),
						'timestamp' => $d->getTimestamp(),
						'format' => '%Y-%m-%d %H',
					];
				}
			}
			else if ($this->_range == Range::WEEK) {
				$this->_period = 'day';
				$this->_format = 'dddd';
				$this->_formatQuery = '%Y-%m-%d';
				
				$d = new DateTime();
				$d->setTime(00, 00, 00);
				$this->_dates[$d->format('Y-m-d')] = [
					'date' => $d->format('Y-m-d'),
					'timestamp' => $d->getTimestamp(),
					'format' => '%Y-%m-%d',
				];
				for ($i = 0; $i < 7; $i++) {
					$d->modify('-1 day');
					$this->_dates[$d->format('Y-m-d')] = [
						'date' => $d->format('Y-m-d'),
						'timestamp' => $d->getTimestamp(),
						'format' => '%Y-%m-%d',
					];
				}
			}
			else if ($this->_range == Range::MONTH) {
				$this->_period = 'day';
				$this->_format = 'DD MMMM';
				$this->_formatQuery = '%Y-%m-%d';
				
				$d = new DateTime();
				$d->setTime(00, 00, 00);
				$this->_dates[$d->format('Y-m-d')] = [
					'date' => $d->format('Y-m-d'),
					'timestamp' => $d->getTimestamp(),
					'format' => '%Y-%m-%d',
				];
				$days = $d->format('d');
				for ($i = 0; $i < $days; $i++) {
					$d->modify('-1 days');
					$this->_dates[$d->format('Y-m-d')] = [
						'date' => $d->format('Y-m-d'),
						'timestamp' => $d->getTimestamp(),
						'format' => '%Y-%m-%d',
					];
				}
			}
			else if ($this->_range == Range::YEAR) {
				$this->_period = 'month';
				$this->_format = 'MMMM';
				$this->_formatQuery = '%Y-%m';
				
				$d = new DateTime();
				$d->setTime(00, 00, 00);
				$this->_dates[$d->format('Y-m')] = [
					'date' => $d->format('Y-m'),
					'timestamp' => $d->getTimestamp(),
					'format' => '%Y-%m',
				];
				for ($i = 0; $i < 11; $i++) {
					$d->modify('-1 month');
					$this->_dates[$d->format('Y-m')] = [
						'date' => $d->format('Y-m'),
						'timestamp' => $d->getTimestamp(),
						'format' => '%Y-%m',
					];
				}
			}
		}
		
		asort($this->_dates);
		
		return $this->_dates;
	}
	
	/**
	 * Get min timestamp in dates
	 * @return int
	 */
	public function getTimestampMin() {
		$min = Arr::minByKey($this->getDates(), 'timestamp');
		$dateMin = new \DateTime();
		$dateMin->setTimestamp($min)->setTime(00, 00, 00);
		return $dateMin->getTimestamp();
	}
	
	/**
	 * Get max timestamp in dates
	 * @return int
	 */
	public function getTimestampMax() {
		$max = Arr::maxByKey($this->getDates(), 'timestamp');
		$dateMax = new \DateTime();
		$dateMax->setTimestamp($max)->setTime(23, 59, 59);
		return $dateMax->getTimestamp();
	}
}