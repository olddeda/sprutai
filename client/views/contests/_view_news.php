<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\News */
/* @var $canVote boolean */
/* @var $showCounters boolean */

$isHideAuthor = (isset($hideAuthorName)) ? true : false;
$isShowType = (isset($showType)) ? true : false;
$linkTarget = (isset($urlTarget)) ? $urlTarget : '_self';

$class = 'panel panel-default';
if ($model->pinned)
	$class .= ' pinned';
?>

<div class="<?= $class ?>">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-8">
				<div class="header-type">
					<?= \common\modules\content\helpers\enum\Type::getLabel($model->type); ?>
				</div>
				<div class="date inline margin-bottom-15">
					<?= Yii::$app->formatter->asDate($model->date_at) ?>
				</div>
				<?php if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()) { ?>
				<div class="edit inline margin-left-10">
					<?= Html::a('['.mb_strtolower(Yii::t('base', 'button_update')).']', ['/content/news/update', 'id' => $model->id]) ?>
				</div>
				<?php } ?>
			</div>
			<div class="col-md-4 align-right">
				<?= $this->render('//statistics/_comments', [
					'model' => $model,
				]) ?>
				<?= $this->render('//statistics/_visit', [
					'model' => $model,
				]) ?>
				<?= $this->render('_view_vote', [
					'model' => $model,
					'canVote' => $canVote,
					'showCounters' => $showCounters,
				]) ?>
			</div>
		</div>
		<div class="header"></div>
		<?php if (!$isHideAuthor) { ?>
			<div class="author">
				<span><?= $model->owner->type ?>:</span>
				<?= Html::a($model->owner->title, $model->owner->url) ?>
			</div>
		<?php } ?>
		<div class="title inline margin-top-0"><?= Html::a($model->title, ['/news/view', 'id' => $model->id], ['target' => $linkTarget]) ?></div>
		<?php if ($model->image->getFileExists()) { ?>
			<div class="image">
				<?= Html::a(Html::img($model->image->getImageSrc(1000, 400, Mode::RESIZE)), ['/news/view', 'id' => $model->id], ['_target' => $linkTarget]) ?>
			</div>
		<?php } ?>
		<div class="text">
			<?= Html::encode($model->descr) ?>
		</div>
		<div class="link">
			<?= Html::a('Читать далее...', ['/news/view', 'id' => $model->id], ['_target' => $linkTarget]) ?>
		</div>
	</div>
</div>