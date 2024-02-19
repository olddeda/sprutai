<?php

use yii\bootstrap\Html;

use common\modules\content\helpers\TabHelper;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\plugin\models\Instruction */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('plugin-instruction', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $this->context->pluginModel->title, 'url' => ['default/view', 'id' => $this->context->pluginModel->id]];
$this->params['breadcrumbs'][] = $this->title;
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

<div class="plugin-default-view detail-view">
	
	<?= $this->render('../default/_header', [
		'model' => $this->context->pluginModel,
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
	
	<?php if (Yii::$app->user->can('plugin.instruction.update')) { ?>
		<div class="form-group margin-20">
			<?= Html::a(Yii::t('base', 'button_update'), ['update', 'plugin_id' => $this->context->pluginModel->id], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	<?php } ?>
	
</div>
