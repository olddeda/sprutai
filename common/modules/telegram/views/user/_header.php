<?php

use common\modules\telegram\models\TelegramUser;
use yii\helpers\Html;
use yii\widgets\Menu;

use common\modules\media\helpers\enum\Mode;

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

/**
 * @var TelegramUser $model
 */

?>

<div class="detail-view-header">
	<div class="wrapper-lg">
		<div class="row m-t">
			<div class="col-sm-7">
				<div class="thumb-lg pull-left m-r">
					<?php if ($model->avatar->hasImage()) { ?>
					<img class="img-circle" src="<?= $model->avatar->getImageSrc(90, 90, Mode::CROP_CENTER) ?>" />
					<?php } else { ?>
					<span class="img-circle img-placeholder"><i class="glyphicon glyphicon-picture width-90 height-90"></i></span>
					<?php } ?>
				</div>
				<div class="clear m-b">
					<div class="m-b m-t-sm">
						<h3><?= $model->getFullname() ?></h3>
					</div>
					<?php if ($model->username || $model->phone || $model->email) { ?>
						<p class="m-b">
							<?php if ($model->username) { ?>
								<?= Html::a(Html::tag('i', '', ['class' => 'fa fa-telegram']), 'tg://resolve?domain='.$model->username, ['class' => 'btn btn-sm btn-bg btn-rounded btn-default btn-icon']) ?>
							<?php } ?>
							<?php if ($model->phone) { ?>
								<?= Html::a(Html::tag('i', '', ['class' => 'fa fa-phone']), 'tel://'.$model->phone, ['class' => 'btn btn-sm btn-bg btn-rounded btn-default btn-icon']) ?>
							<?php } ?>
							<?php if ($model->email) { ?>
								<?= Html::mailto(Html::tag('i', '', ['class' => 'fa fa-envelope']), $model->email , ['class' => 'btn btn-sm btn-bg btn-rounded btn-default btn-icon']) ?>
							<?php } ?>
						</p>
					<?php } ?>
				</div>
			</div>
			<div class="col-sm-5"></div>
		</div>
	</div>
</div>
<div class="detail-view-menu">
	<?= Menu::widget([
		'options' => [
			'class' => 'nav nav-pills nav-sm',
		],
		'items' => [
			['label' => Yii::t('telegram-user', 'menu_general'), 'url' => ['/telegram/user/view', 'id' => $model->id], 'visible' => Yii::$app->user->can('telegram.user.view')],
			['label' => Yii::t('telegram-user', 'menu_requests'), 'url' => ['/telegram/user/request', 'id' => $model->id], 'visible' => Yii::$app->user->can('telegram.user.request')],
			['label' => Yii::t('telegram-user', 'menu_answers'), 'url' => ['/telegram/user/answer', 'id' => $model->id], 'visible' => Yii::$app->user->can('telegram.user.answer')],
			['label' => Yii::t('telegram-user', 'menu_projects'), 'url' => ['/telegram/user/project', 'id' => $model->id], 'visible' => Yii::$app->user->can('telegram.user.project')],
		],
	]) ?>
</div>
