<?php
namespace common\modules\base\widgets\price;

use yii\widgets\InputWidget;

class PriceWidget extends InputWidget
{
	/** @var string */
	public $attribute_currency;
	
	/** @var string */
	public $attribute_request;
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		return $this->render('view', [
			'model' => $this->model,
			'attribute' => $this->attribute,
			'attribute_currency' => $this->attribute_currency,
			'attribute_request' => $this->attribute_request,
		]);
	}
}