<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\components\Helper;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\comments\widgets\CommentWidget;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

use common\modules\payment\Module as ModulePayment;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\content\models\Portfolio */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/companies/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/companies/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-portfolio', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = $model->title;


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

<div class="content-view">
	
	<?= $this->render('../default/_header', [
		'model' => $company,
		'question' => null,
		'portfolio' => $model,
	]) ?>
	
	<?= Html::img(Url::to($model->image->getImageSrc(1000, 400, Mode::RESIZE), true), ['class' => 'preview hidden']) ?>

	<div class="content-index padding-20">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
				<div class="panel panel-default">
					<div class="panel-body">
						<article>
							<?= $this->render('_view_text', [
								'model' => $model,
							]); ?>
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
								'moduleType' => ModuleType::CONTENT_BLOG,
								'options' => ['class' => 'vote vote-visible-buttons']
							]); ?>
						</div>
					</div>

					<div class="favorite panel panel-default">
						<div class="panel-body">
							<?= \common\modules\vote\widgets\Favorite::widget([
								'viewFile' => '@client/views/vote/favorite',
								'entity' => \common\modules\vote\models\Vote::CONTENT_FAVORITE,
								'model' => $model,
								'moduleType' => ModuleType::CONTENT_BLOG,
							]); ?>
						</div>
					</div>

				</div>
				
				<?php if ($model->getTags()->count()) { ?>
					<div class="tags margin-bottom-15">
						<?php foreach ($model->tags as $tag) { ?>
							<?= Html::a($tag->title, ['/tags/blogs', 'title' => $tag->title], ['class' => 'btn btn-primary']) ?>
						<?php } ?>
					</div>
				<?php } ?>

				<div class="content-comments">
					<?= common\modules\comments\widgets\CommentWidget::widget([
						'moduleType' => ModuleType::CONTENT_BLOG,
						'model' => $model,
						'commentView' => '@client/views/comments/index',
						'relatedTo' => Yii::t('comments', 'related_to_text', [
							'title' => $model->title,
							'url' => Url::current(),
						]),
					]); ?>
				</div>
				
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('company-portfolio', 'button_back'), ['index', 'company_id' => $company->id], [
					'class' => 'btn btn-default btn-lg margin-bottom-15'
				]) ?>

			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('../default/_view_contacts', ['model' => $company]) ?>
				<?= $this->render('../default/_view_discount', ['model' => $company]) ?>
				<?= $this->render('../default/_view_questions', ['model' => $company]) ?>
			</div>
		</div>
	</div>
	
</div>
