<?php

/**
 * @var string $selector
 * @var string $paramName
 * @var string $inputName
 * @var array $settings
 */
use common\modules\media\widgets\fileapi\Widget;

?>
<div id="<?= $selector; ?>" class="media-widget media-widget__single">
	<div id="<?= $selector; ?>-img" class="media-widget__avatar">

	</div>

	<div class="media-widget__progress-wrapper" data-media="active.show" style="display: none">
		<div class="media-widget__progress progress progress-success">
			<div class="media-widget__progress-bar" role="progressbar" data-media="progress" aria-valuemin="0" aria-valuemax="100"></div>
		</div>
	</div>
	<div class="media-widget__actions" data-media="active.hide">
		<div class="btn btn-primary media-widget__button-add">
            <span data-media="empty.show">
                <i class="glyphicon glyphicon-folder-open"></i>
				<?= Widget::t('widget', 'Browse..') ?>
            </span>
			<span data-media="name"></span>
			<input type="file" name="<?= $paramName ?>" class="media-widget__input">
		</div>
		<?php if (isset($settings['autoUpload']) && $settings['autoUpload'] == false) { ?>
			<div class="btn btn-success" data-media="empty.hide">
				<span data-media="ctrl.upload"><?= Widget::t('widget', 'Upload') ?></span>
			</div>
		<?php } ?>
		<div class="btn btn-danger" data-media="empty.hide" title="<?= Widget::t('widget', 'Remove') ?>">
			<div data-media="remove">
				<span class="glyphicon glyphicon-trash"></span>
			</div>
		</div>
	</div>

	<div class="alert alert-danger" data-media="error" style="display:none"></div>

	<input type="hidden" name="<?= $inputName ?>" value="" data-media="input" />
</div>

<?php if ($crop === true) { ?>
    <div id="<?= $selector; ?>-crop-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">adasd</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-preview" data-media="crop.preview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-media="crop.save">Save</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
