<?php

use common\modules\menu\widgets\MenuTree;

$this->context->bodyClass = 'white';

?>
<div class="menu-view menu-item">
	<div class="overflow">
		<?= MenuTree::widget([
			'options' => [
				'class' => 'tree',
			],
			'addOption' => false,
			'editOption'=> false,
			'deleteOption' => false,
			'viewOption' => true,
			'fullWidthOption' => '30000px',
			'items' => $model->tree,
			'menuRootTemplate' => '<div class="root">{title}</div>',
			//'menuRootTemplate' => $this->render('template_view'),
			'menuTemplate' => $this->render('template_view'),
		]) ?>
	</div>
</div>
