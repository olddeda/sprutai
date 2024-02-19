<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model \common\modules\content\models\Content */

?>

<div class="grid">
	<div class="col width-100 margin-right-10">
		<?= Html::img($model->company->logo->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
	</div>
	<div class="col width-auto">
		<div class="author margin-bottom-10">
			<h4 class="margin-top-5 margin-bottom-0"><?= Html::a($model->company->title, $url) ?></h4>
			<span><?= $model->company->getTypeName() ?></span>
		</div>
		<?php if ($model->company->address) { ?>
		<div class="address margin-bottom-5">
			<span class="fa fa-globe"></span>
			<?= $model->company->address->country.', '.$model->company->address->city ?>
		</div>
		<?php } ?>
		<?php if ($model->company->site) { ?>
		<div class="telegram margin-bottom-5">
			<span class="fa fa-cloud"></span>
			<?= Html::a($model->company->site, $model->company->site, ['target' => '_blank']) ?>
		</div>
		<?php } ?>
		<?php if ($model->company->email) { ?>
		<div class="telegram margin-bottom-5">
			<span class="fa fa-envelope"></span>
			<?= Html::mailto($model->company->email, $model->company->email) ?>
		</div>
		<?php } ?>
		<?php if ($model->company->phone) { ?>
		<div class="phone margin-bottom-5">
			<span class="fa fa-phone"></span>
			<?= $model->company->phone ?>
		</div>
		<?php } ?>
	</div>
</div>


<hr>
<div class="align-center">
	<?= \common\modules\vote\widgets\Subscribe::widget([
		'viewFile' => '@client/views/vote/subscribe_author',
		'entity' => \common\modules\vote\models\Vote::COMPANY_FAVORITE,
		'model' => $model->company,
		'moduleType' => \common\modules\base\helpers\enum\ModuleType::COMPANY,
		'buttonOptions' => [
			'class' => 'vote-subscribe-author',
			'label' => Yii::t('vote', 'button_favorite_author_add'),
			'labelAdd' => Yii::t('vote', 'button_favorite_author_add'),
			'labelRemove' => Yii::t('vote', 'button_favorite_author_remove'),
		],
	]); ?>
</div>