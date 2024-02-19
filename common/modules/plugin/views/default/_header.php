<?php

use yii\helpers\Html;
use yii\widgets\Menu;

use common\modules\media\helpers\enum\Mode;

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
					<img class="img-circle" src="<?= $model->logo->getImageSrc(90, 90, Mode::CROP_CENTER) ?>" />
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
						<?php if (1 == 2) { ?>
							<?= $this->render('_payment', [
								'model' => $model,
							]) ?>
						<?php } ?>
	                </div>

	                <div class="download margin-top-10">
						<?php if (1 == 2 && $model->getIsPaid()) { ?>
							<?= Html::button(Yii::t('plugin', 'button_download'), [
								'class' => 'btn btn-primary btn-lg'
							]) ?>
						<?php } ?>
	                </div>
	                
                </div>
            </div>
		</div>
	</div>
</div>
<div class="detail-view-menu">
	<?= Menu::widget([
		'options' => [
			'class' => 'nav nav-pills nav-sm',
		],
		'items' => [
			['label' => Yii::t('plugin', 'menu_general'), 'url' => ['/plugin/default/view', 'id' => $model->id], 'visible' => Yii::$app->user->can('plugin.default.view')],
			['label' => Yii::t('plugin', 'menu_instruction'), 'url' => ['/plugin/instruction/index', 'plugin_id' => $model->id], 'visible' => Yii::$app->user->can('plugin.instruction.index')],
			['label' => Yii::t('plugin', 'menu_versions'), 'url' => ['/plugin/version/index', 'plugin_id' => $model->id], 'visible' => Yii::$app->user->can('plugin.version.index')],
			['label' => Yii::t('plugin', 'menu_payers'), 'url' => ['/plugin/payer/index', 'plugin_id' => $model->id], 'visible' => Yii::$app->user->can('plugin.payer.index')],
			['label' => Yii::t('plugin', 'menu_update'), 'url' => ['/plugin/default/update', 'id' => $model->id], 'visible' => Yii::$app->user->can('plugin.default.update')],
		],
	]) ?>
</div>
