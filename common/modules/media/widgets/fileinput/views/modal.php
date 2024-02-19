<?php
use yii\web\View;
/* @var $this \yii\web\View */
?>

<?php
$js = <<<JS
	$('#fileinput-widget-button-save').click(function() {
		$('#fileinput-widget-form').submit();
	});
JS;
$this->registerJS($js, View::POS_END);
?>

<div class="modal fade" id="fileinput-widget-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= Yii::t('media', 'title_update') ?></h4>
			</div>
			<div class="modal-body fileinput-widget-form"></div>
			<div class="modal-footer">
				<button id="fileinput-widget-button-save" type="button" class="btn btn-primary"><?= Yii::t('base', 'button_save') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('base', 'button_close') ?></button>
			</div>
		</div>
	</div>
</div>