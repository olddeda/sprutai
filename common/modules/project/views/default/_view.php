<?php

use yii\widgets\DetailView;

?>

<div class="margin-top-15">
	<?= DetailView::widget([
		'model' => $model->translate($language),
		'attributes' => [
			[
				'attribute' => 'title',
			],
			[
				'attribute' => 'text',
			],
		],
	]) ?>
</div>