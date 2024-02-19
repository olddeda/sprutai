<?php

use yii\widgets\Menu;
?>

<div class="detail-view-menu">
	<?= Menu::widget([
		'options' => [
			'class' => 'nav nav-pills nav-sm',
		],
		'items' => $items,
	]) ?>
</div>
