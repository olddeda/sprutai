<?php
use yii\helpers\Html;

use common\modules\base\assets\FancyboxAsset;

use common\modules\media\helpers\enum\Mode;

FancyboxAsset::register($this);
?>

<?php if($model->hasImages()) { ?>
<div class="images-show-widget ">
<?php foreach ($model->getImages() as $image) { ?>
	<div class="thumbnail">
		<?= Html::beginTag('a', [
			'href' => $image->getImageSrc(2000, 2000, Mode::RESIZE),
			'data-caption' => $image->title,
			'data-fancybox' => 'images',
		]) ?>
		<?= Html::img($image->getImageSrc($width, $height, $mode), ['class' => 'img-responsive']) ?>
		<?= Html::endTag('a') ?>
	</div>
<?php } ?>
</div>
<?php } else { ?>
<p class="empty"><?= Yii::t('media', 'error_images_is_empty') ?></p>
<?php } ?>
