<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this \yii\web\View */
/* @var $model \common\modules\plugin\models\Plugin */

$isHideAuthor = (isset($hideAuthorName)) ? true : false;
$isShowType = (isset($showType)) ? true : false;
$linkTarget = (isset($urlTarget)) ? $urlTarget : '_self';

$class = 'panel panel-default';
if ($model->pinned)
	$class .= ' pinned';
?>

<div class="<?= $class ?>">
	<div class="panel-body">
		
		<?php if ($isShowType) { ?>
		<div class="header-type">
			<?= \common\modules\content\helpers\enum\Type::getLabel($model->type); ?>
		</div>
		<?php } ?>
		
		<div class="grid">
			<div class="col width-200">
				<div class="image">
				<?php if ($model->logo->getFileExists()) { ?>
					<?= Html::a(Html::img($model->logo->getImageSrc(200, 200, Mode::RESIZE)), ['/plugins/view', 'id' => $model->id], ['target' => $linkTarget]) ?>
				<?php } else { ?>
					<span class="img-placeholder"><i class="glyphicon glyphicon-picture width-200 height-100"></i></span>
				<?php } ?>
				</div>
			</div>
			<div class="col width-auto">
				<div class="row">
					<div class="col-md-8">
						<div class="title"><?= Html::a($model->title, ['/plugins/view', 'id' => $model->id], ['target' => $linkTarget, 'class' => 'margin-0']) ?></div>
						<?php if ($model->version) { ?>
						<div class="version">
							<i class="fa fa-code-fork" aria-hidden="true"></i> <?= Html::encode($model->version->version) ?>
						</div>
						<?php } ?>
						<?php if (!$isHideAuthor) { ?>
						<div class="author margin-top-15">
							<span>Автор:</span>
							<?= Html::a($model->authorName, ['/user/content/plugin', 'id' => $model->author_id]) ?>
						</div>
						<?php } ?>
						<div class="text margin-top-15">
							<?= Html::encode($model->descr) ?>
						</div>
					</div>
					<div class="col-md-4 align-right">
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
									'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT_PLUGIN,
									'options' => ['class' => 'vote vote-visible-buttons']
								]); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="link">
			<?= Html::a(Yii::t('plugin', 'link_view'), ['/plugins/view', 'id' => $model->id], ['target' => $linkTarget]) ?>
		</div>
	</div>
</div>