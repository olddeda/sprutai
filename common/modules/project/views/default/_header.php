<?php

use yii\helpers\Html;
use yii\widgets\Menu;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\project\models\Project */

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
                <div class="pull-right">
                    <?= $this->render('_payment', [
                       'model' => $model,
                    ]) ?>
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
			['label' => Yii::t('project', 'menu_general'), 'url' => ['/project/default/view', 'id' => $model->id], 'visible' => Yii::$app->user->can('project.default.view')],
			['label' => Yii::t('project', 'menu_event'), 'url' => ['/project/event/index', 'project_id' => $model->id], 'visible' => Yii::$app->user->can('project.event.index')],
			['label' => Yii::t('project', 'menu_payers'), 'url' => ['/project/payer/index', 'project_id' => $model->id], 'visible' => Yii::$app->user->can('project.payer.index')],
			['label' => Yii::t('project', 'menu_update'), 'url' => ['/project/default/update', 'id' => $model->id], 'visible' => Yii::$app->user->can('project.default.update')],
		],
	]) ?>
</div>
