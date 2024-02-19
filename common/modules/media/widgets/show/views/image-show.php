<?php

use yii\helpers\Html;

?>

<div class="thumbnail" style="width: <?= $width ?>px;height: <?= $height ?>px;">
	<?php $image = $model->getImageSrc($width, $height, $mode); ?>
	<?php if ($image) { ?>
		<?= Html::img($image, ['class' => 'img-responsive']) ?>
	<?php } else { ?>
		<div class="thumbnail-wrapper">
			<div class="glyphicon glyphicon-picture"></div>
		</div>
	<?php } ?>
</div>