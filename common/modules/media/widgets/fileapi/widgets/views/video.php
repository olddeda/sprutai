<?php

/**
 * Single upload view.
 *
 * @var \yii\web\View $this View
 * @var \yii\helpers\Html $input Hidden input
 * @var string $input Hidden input
 * @var string $selector Widget ID selector
 * @var string $paramName The parameter name for the file form data
 * @var string $value Current file name
 * @var boolean $preview Enable/disable preview
 * @var boolean $browseGlyphicon Show/Hide browse glyphicon
 */

use common\modules\media\widgets\fileapi\Widget;

?>
<div id="<?= $selector; ?>" class="uploader">
	<?php if ($preview === true) { ?>
		<a href="#" class="uploader-preview">
			<span data-fileapi="delete" class="uploader-preview-delete">
				<span class="glyphicon glyphicon-trash"></span>
			</span>
			<span data-fileapi="preview" class="uploader-preview-wrapper"></span>
		</a>
	<?php } ?>
	<div class="btn btn-default js-fileapi-wrapper col-sm-12">
		<div class="uploader-browse" data-fileapi="active.hide">
			<?php if ($browseGlyphicon === true) { ?>
				<span class="glyphicon glyphicon-video"></span>
			<?php } ?>
			<span data-fileapi="browse-text" class="<?= $value ? 'hidden' : 'browse-text' ?>">
				<?= Yii::t('media-fileapi', 'button_select_video') ?>
			</span>
			<span data-fileapi="name"></span>
			<input type="file" name="<?= $paramName ?>">
		</div>
		<div class="uploader-progress" data-fileapi="active.show">
			<div class="progress progress-striped">
				<div class="uploader-progress-bar progress-bar progress-bar-info" data-fileapi="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</div>
	</div>
	<?= $input ?>
</div>