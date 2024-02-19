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
	<div class="pull-left">
		<?= Menu::widget([
			'options' => [
				'class' => 'nav nav-pills nav-sm',
			],
			'activateParents' => true,
			'items' => [
				['label' => Yii::t('project', 'menu_general'), 'url' => ['/projects/default/view', 'id' => $model->id]],
				['label' => Yii::t('project', 'menu_event'), 'url' => ['/projects/default/event', 'id' => $model->id]],
				['label' => Yii::t('project', 'menu_questions'), 'url' => ['/projects/question/index', 'project_id' => $model->id], 'items' => [
					['label' => '', 'url' => ['/projects/question/view', 'project_id' => $model->id, 'id' => (isset($question) && $question ? $question->id : 0)], 'options' => ['style' => 'display:none']],
					['label' => '', 'url' => ['/projects/question/create', 'project_id' => $model->id], 'options' => ['style' => 'display:none']],
					['label' => '', 'url' => ['/projects/question/update', 'project_id' => $model->id, 'id' => (isset($question) && $question ? $question->id : 0)], 'options' => ['style' => 'display:none']],
				]],
				['label' => Yii::t('project', 'menu_payments'), 'url' => ['/projects/default/payment', 'id' => $model->id]],
			],
		]) ?>
	</div>
	<div class="pull-right">
		<?= \common\modules\vote\widgets\Favorite::widget([
			'viewFile' => '@client/views/vote/favorite',
			'entity' => \common\modules\vote\models\Vote::CONTENT_FAVORITE,
			'model' => $model,
			'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT_PROJECT,
		]); ?>
	</div>
	<div class="clearfix"></div>
</div>
