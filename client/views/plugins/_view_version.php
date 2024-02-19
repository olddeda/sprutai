<?php

use yii\bootstrap\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $model common\modules\plugin\models\Version */

?>

<a class="pull-left thumb-sm avatar m-l-n-md">
	<?php $imageSrc = ($model->plugin->author->avatar->getFileExists()) ? $model->plugin->author->avatar->getImageSrc(100, 100, Mode::CROP_CENTER) : Yii::$app->request->baseUrl.'/images/svg/cover_avatar.svg'; ?>
	<?= Html::img($imageSrc, ['class' => 'img-thumbnail img-circle']) ?>
</a>
<div class="m-l-xl m-b-lg panel b-a bg-light lt">
	<div class="padding-15 pos-rlt">
		<span class="arrow left pull-up"></span>
		<strong class="text-primary"><?= $model->datetime ?></strong>
		<br/>
		<strong><?= $model->version ?></strong>
	</div>
	<div class="panel-body padding-top-5">
		<div><?= $model->text ?></div>
		
		<?php if ($model->plugin->getCanDownload()) { ?>
			<?= Html::a(Yii::t('plugin', 'button_download_version', ['version' => $model->version]), $model->getDownloadUrl(), [
				'class' => 'btn btn-primary btn-sm'
			]) ?>
		<?php } ?>
	</div>
</div>