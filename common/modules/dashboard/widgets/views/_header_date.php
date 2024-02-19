<?php

use yii\bootstrap\ButtonDropdown;

use common\modules\dashboard\components\DashboardWidget;
use common\modules\dashboard\helpers\enum\Range;

/* @var $this yii\web\View */

?>

<div class="pull-right btn-group">
	<?= ButtonDropdown::widget([
    	'label' => Range::getLabel($this->context->getParam('range', Range::DAY)),
    	'dropdown' => [
        	'items' => [
           	 	['label' => Range::getLabel(Range::DAY), 'url' => ['index', 'widget' => $this->context->getName(), 'range' => Range::DAY]],
				['label' => Range::getLabel(Range::WEEK), 'url' => ['index', 'widget' => $this->context->getName(), 'range' => Range::WEEK]],
				['label' => Range::getLabel(Range::MONTH), 'url' => ['index', 'widget' => $this->context->getName(), 'range' => Range::MONTH]],
				['label' => Range::getLabel(Range::YEAR), 'url' => ['index', 'widget' => $this->context->getName(), 'range' => Range::YEAR]],
        	],
    	],
		'options' => [
			'class' => 'dropdown-toggle btn btn-default btn-sm',
		],
	]) ?>
</div>
<div class="panel-title"><?= $this->context->getTitle() ?></div>