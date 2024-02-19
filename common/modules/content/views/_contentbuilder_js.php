<?php

use common\modules\tag\helpers\enum\Type as TagType;
use common\modules\tag\models\Tag;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */
/* @var $field string */
/* @var $validateTag boolean|null */
/* @var $validateSpecial boolean|null */

?>

<?php

$userId = Yii::$app->user->id;
$modelName = $model->getModuleClass();
$name = strtolower($modelName);

if (!isset($field)) {
	$field = 'text';
}

$isPublished = (int)$model->getIsPublished();
$tagsSpecial = json_encode(array_keys(Tag::listDataType('id', 'title', 'title', [TagType::SYSTEM])));
$isValidateTag = (int)$validateTag;
$isValidateSpecial = (int)$validateSpecial;

$authorCompleteFields = ($model->hasMethod('checkAuthorCompleteFields')) ? $model->checkAuthorCompleteFields() : [];
$authorCompleteFieldsJson = Json::encode($authorCompleteFields);

$messageEmptyTags = Yii::t('content', 'message_empty_tags');
$messageEmptySpecialTag = Yii::t('content', 'message_empty_special_tag');

$customVal = base64_encode(serialize([
	'hash' => $model->hash,
	'module_type' => $model->getModuleType(),
]));

$js = <<<JS
var timerAutosaveId;
var contentId = 'contentbuilder-$model->unique_id-$model->updated_at-$userId';
var isPublished = $isPublished;
var tagsSpecial = $tagsSpecial;
var modelName = '$modelName';
var isValidateTag = $isValidateTag;
var isValidateSpecial = $isValidateSpecial;
var authorCompleteFields = $authorCompleteFieldsJson;

function contentBuilderSave(button) {
	contentBuilderAutosaveStop();
	
	buttonsStateWait(true, button);
	
	var obj = $('#content-builder-field');
	obj.saveimages({
		handler: '/client/media/default/upload-content-builder',
		customval: '$customVal',
		onComplete: function (result) {
			if (result) {
				var sHTML = obj.data('contentbuilder').html();
				var sHTMLClean = codeMirrorClear(sHTML);
				
				$('#$name-$field').val(sHTMLClean);
				
				localStorage.setItem(contentId, null);
				
				buttonsStateWait(false, button);
				
				$('#form-content').submit();
			}
			else {
				buttonsStateWait(false, button);
				
				contentBuilderAutosaveStart();
			}
		}
	});
	obj.data('saveimages').save();
}

function contentBuilderAutosaveStart() {
	timerAutosaveId = setInterval(function() {
		var obj = $('#content-builder-field');
		var sHTML = obj.data('contentbuilder').html();
		var sHTMLClean = codeMirrorClear(sHTML);
		localStorage.setItem(contentId, sHTMLClean);
	}, 10000);
}

function contentBuilderAutosaveStop() {
	clearInterval(timerAutosaveId);
}

$('#button-content-submit, #button-content-moderate, #button-content-draft').click(function() {
	var button = $(this);
	
	var status = parseInt($('#$name-status').val());
	var newStatus = status;
	if (button.attr('name') == 'draft')
		newStatus = 5;
	else if (button.attr('name') == 'moderate')
		newStatus = 4;
	else if (button.attr('name') == 'publish')
		newStatus = 1;
	if (newStatus != status) {
		status = newStatus;
		$('#$name-status').val(status).trigger('change');
	}
	
	if (isValidateTag && !isPublished && button.attr('id') == 'button-content-submit' && status == 1) {
		var selectize = $('#$name-tags_ids')[0].selectize;
		
		var ids = selectize.items;
		if (!ids.length) {
			yii.confirm('$messageEmptyTags', function() {
				contentBuilderSave($(this));
			});
			return;
		}
		
		var isFound = false;
		if (ids) {
			$.each(ids, function(key, itemId) {
				itemId = parseInt(itemId);
				console.log('i - ', itemId, $.inArray(itemId, tagsSpecial));
				if ($.inArray(itemId, tagsSpecial) !== -1) {
					isFound = true;
				}
			});
		}
		if (!isFound && isValidateSpecial) {
			yii.confirm('$messageEmptySpecialTag', function() {
				contentBuilderSave($(this));
			});
			return;
		}
	}
	
	contentBuilderSave($(this));
});

$('#form-content').on('afterValidate', function (event, attribute, messages, deferreds) {
	$.each(attribute, function(key, val) {
		if (val.length) {
			notifyError(val[0]);
			
			buttonsStateWait(false);
			
			return;
		}
	});
});

function buttonsStateWait(isWait, currentButton) {
	var buttons = [$('#button-content-draft'), $('#button-content-moderate'), $('#button-content-submit'), $('#button-content-back')];
	$.each(buttons, function(key, button) {
		if (isWait)
			button.attr('disabled', 'disable');
		else
			button.removeAttr('disabled');
		
		var html = (isWait && button[0] == currentButton[0] ? button.data('title-wait') : button.data('title-original'));
		button.html(html);
	});
}
JS;
$this->registerJs($js);
?>

<?php
if (isset($model->errors['validate_author_fields'])) {
	$err = $model->validate_author_fields;
	Modal::begin([
		'id' => 'modal-content-moderate-warning',
		'header' => '<h4 class="modal-title">'.Yii::t('content', 'modal_moderate_warning_title').'</h4>',
	]);
	
	$urlProfile = Url::to(['/user/settings']);
	$urlAddress = Url::to(['/user/address/index']);
	$urlTelegram = Url::to(['/article/215']);
	
	echo Html::tag('p', Yii::t('content', 'modal_moderate_warning_message'));
	
	echo Html::beginTag('ul');
	if (in_array('avatar', $err))
		echo Html::tag('li', Yii::t('content', 'modal_moderate_warning_field_avatar', ['url' => $urlProfile]));
	if (in_array('first_name', $err))
		echo Html::tag('li', Yii::t('content', 'modal_moderate_warning_field_first_name', ['url' => $urlProfile]));
	if (in_array('last_name', $err))
		echo Html::tag('li', Yii::t('content', 'modal_moderate_warning_field_last_name', ['url' => $urlProfile]));
	if (in_array('address', $err))
		echo Html::tag('li', Yii::t('content', 'modal_moderate_warning_field_address', ['url' => $urlAddress]));
	if (in_array('telegram', $err))
		echo Html::tag('li', Yii::t('content', 'modal_moderate_warning_field_telegram', ['url' => $urlTelegram]));
	echo Html::endTag('ul');
	
	Modal::end();
	$this->registerJs("$('#modal-content-moderate-warning').modal('show')");
}
?>

