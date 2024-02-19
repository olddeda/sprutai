<?php

use common\modules\content\helpers\TabHelper;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\project\models\Project */
/* @var $paymentModel common\modules\payment\models\Payment */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('project', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['index']];
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

<div class="project-default-view detail-view">
	
	<?= $this->render('_header', [
        'model' => $model,
    ]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			<article>
				<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
					<?= $model->text ?>
				</div>
			</article>
		</div>
	</div>
</div>
