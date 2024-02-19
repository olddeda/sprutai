<?php
namespace common\modules\base\widgets\counters\mailrating;

use yii\base\Widget;

class MailRatingCounter extends Widget
{
	const TYPE_ASYNC    	= 'async';
	
	public $counterId		= null;
	public $type      		= self::TYPE_ASYNC;
	public $noscript		= true;
	
	private $_viewFile   	= null;
	private $_viewParams 	= [];
	
	public function init() {
		parent::init();
		
		$this->_viewParams = [
			'counterId' => $this->counterId,
		];
		
		$viewFileMap = [
			self::TYPE_ASYNC => 'async',
		];
		
		$this->_viewFile = isset($viewFileMap[$this->type]) ? $viewFileMap[$this->type] : null;
	}
	
	
	public function run() {
		assert($this->_viewFile !== null);
		
		echo $this->render($this->_viewFile, $this->_viewParams);
		if ($this->noscript) {
			echo $this->render('noscript', ['counterId' => $this->counterId]);
		}
	}
	
	
}
