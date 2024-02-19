<?php

use yii\helpers\Html;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $project common\modules\project\models\Project */
/* @var $model common\modules\content\models\Event */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-event', 'title'), 'url' => ['event', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php
ContentBuilderAsset::register($this);
ContentBuilderContentAsset::register($this);
ContentBuilderSimpleLightBoxAsset::register($this);

$js = <<<JS
    contentbuilderLocalize();

    $('a.is-lightbox').simpleLightbox();

    $('code.code').each(function () {
         codeMirrorHighlight($(this));
    });
JS;
$this->registerJs($js);
?>

<div class="project-default-view detail-view content-view">
	
	<?= $this->render('_header', [
		'model' => $project,
	]) ?>
	
	<div class="margin-top-20 margin-left-20 margin-right-20 margin-bottom-0">
		<div class="panel panel-default">
			<div class="panel-body">
				<article>
					<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
						<?= $model->text ?>
					</div>
				</article>
			</div>
		</div>
		
		<div class="votes">
			<?= $this->render('//statistics/_visit_panel', [
				'model' => $model,
			]) ?>
			
			<div class="vote panel panel-default">
				<div class="panel-body">
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
		
		<?php if ($model->getTags()->count()) { ?>
			<div class="tags margin-bottom-15">
				<?php foreach ($model->tags as $tag) { ?>
					<?= Html::a($tag->title, ['/tags/projects', 'title' => $tag->title], ['class' => 'btn btn-primary']) ?>
				<?php } ?>
			</div>
		<?php } ?>
		
		<div class="content-comments">
			<?= common\modules\comments\widgets\CommentWidget::widget([
				'model' => $model,
				'moduleType' => \common\modules\base\helpers\enum\ModuleType::CONTENT_PROJECT,
				'commentView' => '@client/views/comments/index',
				'relatedTo' => Yii::t('comments', 'related_to_text', [
					'title' => $model->title,
					'url' => \yii\helpers\Url::current(),
				]),
			]); ?>
		</div>
	</div>

</div>