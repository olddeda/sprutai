<?php
namespace common\modules\base\extensions\dynagrid;

use kartik\dynagrid\DynaGrid as BaseDynaGrid;
use yii\data\ActiveDataProvider;

class DynaGrid extends BaseDynaGrid
{
	
	/**
	 * Applies the page size
	 */
	protected function applyPageSize() {
		
		/** @var \yii\data\BaseDataProvider $dataProvider */
		$dataProvider = $this->gridOptions['dataProvider'];
		if (isset($this->_pageSize) && $this->_pageSize !== '' && $this->allowPageSetting) {
			
			/** @var \yii\data\BaseDataProvider $dataProvider */
			$dataProvider = $this->gridOptions['dataProvider'];
			
			if ($dataProvider instanceof ArrayDataProvider) {
				$dataProvider->refresh();
			}
			
			if ($this->_pageSize > 0) {
				$dataProvider->setPagination(['pageSize' => $this->_pageSize]);
			}
			else {
				$dataProvider->setPagination(false);
			}
			
			if ($dataProvider instanceof SqlDataProvider) {
				$dataProvider->prepare(true);
			}
			
			if ($dataProvider instanceof ActiveDataProvider) {
				$type = 'page';
				foreach($_GET as $key => $value) {
					if (strpos($key, '_tog') !== false) {
						$type = $value;
					}
				}
				
				$pageSize = $this->_pageSize;
				if ($type == 'all')
					$pageSize = $dataProvider->totalCount;
				
				$dataProvider->pagination->pageSize = $pageSize;
				$dataProvider->prepare(true);
			}
			
			$this->gridOptions['dataProvider'] = $dataProvider;
		}
	}
}