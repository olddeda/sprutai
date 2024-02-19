<?php

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\Company */

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

<div class="panel panel-default">
	<div class="panel-body">
		<article>
			<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
				<?= $model->text ?>
			</div>
		</article>
	</div>
</div>