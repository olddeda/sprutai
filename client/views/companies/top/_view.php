<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $url array */
/* @var $model \common\modules\company\models\Company */

?>

<div class="grid">
	<div class="col width-100 margin-right-10">
		<?= Html::img($model->logo->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
	</div>
	<div class="col width-auto">
		<div class="author margin-bottom-10">
			<h4 class="margin-top-5 margin-bottom-0"><?= Html::a($model->title, ['companies/default/view', 'id' => $model->id]) ?></h4>
			<span><?= $model->getTypeName() ?></span>
		</div>
		<?php if ($model->address) { ?>
		<div class="address margin-bottom-5">
			<span class="fa fa-globe"></span>
			<?= $model->address->country.', '.$model->address->city ?>
		</div>
		<?php } ?>
		<?php if ($model->site) { ?>
		<div class="telegram margin-bottom-5">
			<span class="fa fa-cloud"></span>
			<?= Html::a($model->site, $model->site, ['target' => '_blank']) ?>
		</div>
		<?php } ?>
		<?php if ($model->email) { ?>
		<div class="telegram">
			<span class="fa fa-envelope"></span>
			<?= Html::mailto($model->email, $model->email) ?>
		</div>
		<?php } ?>
	</div>
</div>

<div class="align-center margin-top-15">
	<?= \common\modules\vote\widgets\Subscribe::widget([
		'viewFile' => '@client/views/vote/subscribe_author',
		'entity' => \common\modules\vote\models\Vote::COMPANY_FAVORITE,
		'model' => $model,
		'moduleType' => \common\modules\base\helpers\enum\ModuleType::COMPANY,
		'buttonOptions' => [
			'class' => 'vote-subscribe-author',
			'label' => Yii::t('vote', 'button_favorite_author_add'),
			'labelAdd' => Yii::t('vote', 'button_favorite_author_add'),
			'labelRemove' => Yii::t('vote', 'button_favorite_author_remove'),
		],
	]); ?>
</div>

<hr>