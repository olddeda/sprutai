<?php

use common\modules\base\extensions\tree\TreeAsset;
use common\modules\base\extensions\select2\Select2Asset;

use yii\helpers\Url;

/* @var $this yii\web\View */

TreeAsset::register($this);

$urlSearch = Url::to(['tag/default/search']);

$js = <<<JS
$('.tree').tree({
	'add_option': true,
	'edit_option': true,
	'delete_option': true,
	'confirm_before_delete': true,
	'animate_option': true,
	'fullwidth_option': false,
	'align_option': 'center',
	'draggable_option': true,
	'url': {
		'search': '$urlSearch'
	}
});
JS;
$this->registerJs($js);
?>

<div class="overflow">
	<div>
		<ul class="tree">
			<li>
				<div class="root">Путеводитель</div>
				<ul>
					<li>
						<div id="20">Athom Homey</div>
						<ul>
							<li>
								<div id="21">Инструкции</div>
							</li>
							<li>
								<div id="22">Flow</div>
							</li>
							<li>
								<div id="23">Плагины</div>
							</li>
						</ul>
					</li>
					<li>
						<div id="50">deCONZ</div>
					</li>
					<li>
						<div id="30">Home Assistant</div>
						<ul>
							<li>
								<div id="31">Инструкции</div>
							</li>
							<li>
								<div id="32">Конфиги</div>
							</li>
							<li>
								<div id="33">Плагины</div>
							</li>
						</ul>
					</li>
					<li>
						<div id="40">Homebridge</div>
						<ul>
							<li>
								<div id="41">Инструкции</div>
							</li>
							<li>
								<div id="42">Плагины</div>
							</li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>