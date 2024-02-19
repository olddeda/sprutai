<?php

use yii\bootstrap\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $model common\modules\payment\models\Payment */

?>

<a class="pull-left thumb-sm avatar m-l-n-md">
	<?php $imageSrc = ($model->user->avatar->getFileExists()) ? $model->user->avatar->getImageSrc(100, 100, Mode::CROP_CENTER) : Yii::$app->request->baseUrl.'/images/svg/cover_avatar.svg'; ?>
	<?= Html::img($imageSrc, ['class' => 'img-thumbnail img-circle']) ?>
</a>
<div class="m-l-xl m-b-lg panel b-a bg-light lt">
	<div class="padding-15 pos-rlt">
		<span class="arrow left pull-up"></span>
		<strong class="text-primary"><?= $model->datetime ?></strong>
		<br/>
		<strong><?= $model->user->getAuthorName() ?></strong>
	</div>
	<div class="panel-body padding-top-5">
		<div><?= Yii::t('project', 'message_payment_paid', ['price' => Yii::$app->formatter->asCurrency($model->price), 'type' => $model->type->title]) ?></div>
	</div>
</div>