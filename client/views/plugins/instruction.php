<?php

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\plugin\models\Plugin */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title.' - '.Yii::t('plugin', 'title_instruction');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['index']];
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

<div class="plugin-default-view detail-view content-view">
	
	<?= $this->render('_header', [
		'model' => $model,
	]) ?>
	
	<div class="panel panel-default margin-20">
		<div class="panel-body">
			<article>
				<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
					<?= ($model->instruction) ? $model->instruction->text : '' ?>
				</div>
			</article>
		</div>
	</div>
	
	<div class="votes margin-top-20 margin-left-20 margin-right-20 margin-bottom-0">
		<div class="vote panel panel-default">
			<div class="panel-body">
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
