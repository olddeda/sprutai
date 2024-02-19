<?php
namespace common\modules\base\widgets\year;

use yii\widgets\InputWidget;

class YearWidget extends InputWidget
{
	/** @var string */
	public $from;
	
	/** @var string */
	public $to;
	
	/** @var  string */
	public $placeholder_from;
	
	/** @var  string */
	public $placeholder_to;
	
	/**
	 * Initializes the widget.
	 * If you override this method, make sure you call the parent implementation first.
	 */
	public function init() {
		if (!$this->from) {
			throw new InvalidConfigException("'from' property must be specified.");
		}
		if (!$this->to) {
			throw new InvalidConfigException("'to' property must be specified.");
		}
		
		parent::init();
	}
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		return $this->render('view', [
			'model' => $this->model,
			'from' => $this->from,
			'to' => $this->to,
			'placeholder_from' => $this->placeholder_from,
			'placeholder_to' => $this->placeholder_to,
		]);
	}
}