<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

use common\modules\media\helpers\enum\Mode;

use common\modules\banner\models\Banner;

/* @var $this yii\web\View */
/* @var $showLeaders bool */

$banners = Banner::getActives();
?>

<?php if (count($banners)) { ?>
	<?php foreach ($banners as $banner) { ?>
		<?php $banner->updateViews() ?>
		<div class="panel panel-default">
			<div class="panel-body">
				<?= Html::a(Html::img($banner->image->getImageSrc(1000, 521, Mode::RESIZE_WIDTH), ['class' => 'img-responsive']), Url::toRoute(['banner/default/visit', 'id' => $banner->id])) ?>
			</div>
		</div>
	<?php } ?>
	<?php
	$banner = Banner::getActive();
	if ($banner && $showLeaders) {
		?>
		
		<?= $this->render('_view_article', ['banner' => $banner]) ?>
		<?= $this->render('_view_blog', ['banner' => $banner]) ?>
	
	<?php } ?>

<?php } ?>