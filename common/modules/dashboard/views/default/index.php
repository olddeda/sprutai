<?php

use common\modules\base\extensions\gridstack\Gridstack;
use yii\helpers\Url;

$this->context->bodyClass = 'dashboard';
$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('dashboard', 'title');
?>

<?php
$url = Url::toRoute('/dashboard/default/save');
$js = <<<JS
$(document).ready(function() {
	$('.grid-stack').on('change', function (e, items) {
		var data = [];
		$.each(items, function(index, obj) {
			var el = $('.grid-stack-item-content', obj.el);
			data.push({
				'name': el.data('name'),
				'x': obj.x,
				'y': obj.y,
				'width': obj.width,
				'height': obj.height
			});
		});
		
		$.ajax({
			url: '$url',
			type: 'POST',
			dataType: 'json',
			data: {data: data},
		});
	});
});
JS;
$this->registerJs($js);
?>

<?php $gridstack = Gridstack::begin([
	'options' => ['class'=>'grid-stack'],
	'clientOptions' => [
		'cellHeight'=> 50,
		'verticalMargin' => 10,
	],
]);?>

<?php
foreach ($this->context->module->widgets as $widget) {
	echo $widget::widget(['gridstack' => $gridstack]);
} ?>

<?php Gridstack::end(); ?>
