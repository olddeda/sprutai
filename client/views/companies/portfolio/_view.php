<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Portfolio */

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
			<div class="col-md-8 col-xs-6">
				<?php if ($isShowType) { ?>
					<div class="header-type">
						<?= \common\modules\content\helpers\enum\Type::getLabel($model->type); ?>
					</div>
				<?php } ?>
				<div class="date inline margin-bottom-15">
					<?= Yii::$app->formatter->asDate($model->date_at) ?>
				</div>
			</div>
			<div class="col-md-4 col-xs-6 align-right">
				<?= $this->render('//statistics/_comments', [
					'model' => $model,
				]) ?>
				<?= $this->render('//statistics/_visit', [
					'model' => $model,
				]) ?>
				<div class="votes inline">
					<div class="vote">
						<?= \common\modules\vote\widgets\Vote::widget([
							'viewFile' => '@client/views/vote/vote',
							'entity' => \common\modules\vote\models\Vote::CONTENT_VOTE,
							'model' => $model,
							'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT,
							'options' => ['class' => 'vote vote-visible-buttons']
						]); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="header"></div>
		<?php if (!$isHideAuthor) { ?>
		<div class="author">
			<span><?= $model->owner->type ?>:</span>
			<?= Html::a($model->owner->title, $model->owner->url) ?>
		</div>
		<?php } ?>
		<div class="title inline margin-top-0"><?= Html::a($model->title, ['/companies/portfolio/view', 'company_id' => $model->company_id, 'id' => $model->id], ['target' => $linkTarget]) ?></div>
		<div class="image">
			<?= Html::a(Html::img($model->image->getImageSrc(1000, 400, Mode::RESIZE)), ['/companies/portfolio/view', 'company_id' => $model->company_id, 'id' => $model->id], ['_target' => $linkTarget]) ?>
		</div>
		<div class="text">
			<?= Html::encode($model->descr) ?>
		</div>
		<div class="link">
			<?= Html::a('Читать далее...', ['/companies/portfolio/view', 'company_id' => $model->company_id, 'id' => $model->id], ['_target' => $linkTarget]) ?>
		</div>
	</div>
</div>