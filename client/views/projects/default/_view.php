<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\project\models\Project */

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
				<?php if ($model->image->getFileExists()) { ?>
					<div class="image">
						<?= Html::a(Html::img($model->image->getImageSrc(200, 200, Mode::RESIZE)), ['/projects/view', 'id' => $model->id], ['target' => $linkTarget]) ?>
					</div>
				<?php } ?>
			</div>
			<div class="col width-auto">
				<div class="row">
					<div class="col-md-8">
						<div class="title"><?= Html::a($model->title, ['/projects/default/view', 'id' => $model->id], ['target' => $linkTarget]) ?></div>
						<div class="text">
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
									'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT_PROJECT,
									'options' => ['class' => 'vote vote-visible-buttons']
								]); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="link">
			<?= Html::a(Yii::t('project', 'link_view'), ['/projects/default/view', 'id' => $model->id], ['target' => $linkTarget]) ?>
		</div>
	</div>
</div>