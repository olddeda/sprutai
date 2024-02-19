<?php

use common\modules\media\helpers\enum\Mode;
use yii\helpers\Html;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $model common\modules\plugin\models\Plugin */

$style = ($model->background->hasImage()) ? 'background-image: url('.$model->background->getImageSrc(2000, 2000, Mode::RESIZE).');' : '';
$bgOpacity = ($model->background->hasImage()) ? 'bg-white-opacity' : '';
?>

<div class="detail-view-header" style="<?= $style ?>">
	<div class="wrapper-lg <?= $bgOpacity ?>">
		<div class="row m-t">
			<div class="col-sm-7">
				<div class="thumb-lg pull-left m-r">
					<?php if ($model->logo->hasImage()) { ?>
					<img class="img-circle" src="<?= $model->logo->getImageSrc(90, 90, Mode::CROP_CENTER) ?>" />
					<?php } else { ?>
					<span class="img-circle img-placeholder"><i class="glyphicon glyphicon-picture width-90 height-90"></i></span>
					<?php } ?>
				</div>
				<div class="clear m-b">
					<div class="m-b m-t-sm">
						<h3><?= $model->title ?></h3>
					</div>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="pull-right align-right">

					<div class="payment align-left">
						<?php if (1 == 2 && $model->getCanPaid()) { ?>
							<?= $this->render('_payment', [
								'model' => $model,
							]) ?>
						<?php } ?>
					</div>

					<div class="download margin-top-10">
						<?php if ($model->getCanDownload()) { ?>
							<?= Html::a(Yii::t('plugin', 'button_download'), $model->getDownloadUrl(), [
								'class' => 'btn btn-primary btn-lg'
							]) ?>
							<div>
								<label><?= Yii::t('plugin', 'label_version', ['version' => $model->version->version]) ?></label>
							</div>
						<?php } ?>
					</div>

				</div>
            </div>
		</div>
	</div>
</div>
<div class="detail-view-menu">
	<div class="pull-left">
		<?= Menu::widget([
			'options' => [
				'class' => 'nav nav-pills nav-sm',
			],
			'items' => [
				['label' => Yii::t('plugin', 'menu_general'), 'url' => ['/plugins/view', 'id' => $model->id]],
				['label' => Yii::t('plugin', 'menu_instruction'), 'url' => ['/plugins/instruction', 'id' => $model->id]],
				['label' => Yii::t('plugin', 'menu_versions'), 'url' => ['/plugins/version', 'id' => $model->id]],
				['label' => Yii::t('plugin', 'menu_payments'), 'url' => ['/plugins/payment', 'id' => $model->id]],
				//['label' => Yii::t('project', 'menu_comments'), 'url' => ['/projects/comment', 'id' => $model->id]],
			],
		]) ?>
	</div>
	<div class="pull-right">
	
	</div>
	<div class="clearfix"></div>
</div>
