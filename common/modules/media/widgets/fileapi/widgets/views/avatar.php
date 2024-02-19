<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

use common\modules\media\helpers\enum\Mode;

/**
 * Avatar upload view.
 *
 * @var \yii\web\View $this View
 * @var \yii\helpers\Html $input Hidden input
 * @var string $input Hidden input
 * @var string $selector Widget ID selector
 * @var string $paramName The parameter name for the file form data
 * @var string $value Current file name
 * @var boolean $preview Enable/disable preview
 * @var boolean $crop Enable/disable crop
 * @var boolean $browseGlyphicon Show/Hide browse glyphicon
 * @var array $settings Settings
 */

?>
	<div id="<?= $selector; ?>" class="uploader uploader-avatar" data-width="<?= $width ?>" data-height="<?= $height ?>" data-format="<?= $format ?>">
		<?php Yii::$app->user->isAdmin ?>
		<?php if ($preview === true) { ?>
		<?php $fileExists = $media->fileExists; ?>
		<div class="uploader-preview">
			<div class="uploader-preview-placeholder thumbnail" style="height:<?= $height + 10 ?>px;">
				<?php if ($fileExists) { ?>
					<?= Html::img($media->getImageSrc($width, $height, Mode::CROP_CENTER)) ?>
				<?php } ?>
				<div class="uploader-preview-placeholder-wrapper <?= ($fileExists) ? 'hidden' : '' ?>">
					<span class="uploader-preview-placeholder-icon glyphicon glyphicon-user" style="font-size:<?= (($width + 10) - ceil((($width + 10) / 100 * 35))) ?>px;"></span>
				</div>
			</div>
			<span data-fileapi="preview" class="uploader-preview-wrapper thumbnail hidden" style="height:<?= ($height + 10) ?>px;"></span>
			<span data-fileapi="delete" class="uploader-preview-delete <?= ($fileExists) ? '' : 'hidden' ?>">
				<span class="glyphicon glyphicon-trash"></span>
			</span>
			<span data-fileapi="delete-loading" class="uploader-preview-delete-loading hidden">
				<span></span>
			</span>
		</div>
		<?php } ?>
		<div class="uploader-actions js-fileapi-wrapper">
			<div class="uploader-browse btn btn-primary" data-fileapi="active.hide">
				<span data-fileapi="browse-text" class="browse-text">
                    <?= Yii::t('media-fileapi', 'button_select_avatar') ?>
                </span>
				<input type="file" name="<?= $paramName ?>">
				<input type="hidden" name="media_hash" value="<?= $media->hash ?>">
				<input type="hidden" name="delete_url" value="<?= Url::toRoute('/media/default/delete') ?>">
			</div>
			<div class="uploader-progress" data-fileapi="active.show">
				<div class="progress progress-striped active">
					<div class="uploader-progress-bar progress-bar progress-bar-primary" data-fileapi="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
		</div>
		<?= $input ?>
	</div>

<?php if ($crop === true) { ?>
	<div id="modal-crop" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"><?= Yii::t('media-fileapi', 'modal_crop_title') ?></h4>
				</div>
				<div class="modal-body">
					<div id="modal-preview"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default cancel"><?= Yii::t('media-fileapi', 'modal_crop_button_cancel') ?></button>
					<button type="button" class="btn btn-primary crop"><?= Yii::t('media-fileapi', 'modal_crop_button_save') ?></button>
				</div>
			</div>
		</div>
	</div>
<?php } ?>