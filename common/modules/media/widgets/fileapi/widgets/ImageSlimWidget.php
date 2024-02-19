<?php
namespace common\modules\media\widgets\fileapi\widgets;

use Yii;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;

use common\modules\base\components\Debug;
use common\modules\base\extensions\slim\SlimWidget;
use yii\web\JsExpression;

class ImageSlimWidget extends SlimWidget
{
	/**
	 * @var integer Media type
	 */
	public $mediaType;
	
	/** @var array  */
	public $settings = [];
	
	public $mediaHash;
	
	/**
	 * @var boolean Enable/disable crop
	 */
	public $crop = false;
	
	public function init() {
		parent::init();
		
		if (isset($this->settings['size']) && isset($this->settings['size']['width']) && isset($this->settings['size']['height']))
			$this->settings['ratio'] = $this->settings['size']['width'].':'.$this->settings['size']['height'];
		
		// Set url
		if (!isset($this->settings['service']))
			$this->settings['service'] = Url::toRoute('/media/default/upload-slim');
		else
			$this->settings['service'] = Url::to($this->settings['service']);
		
		$this->settings['edit'] = false;
		$this->settings['push'] = true;
		$this->settings['instantEdit'] = true;
		$this->settings['devicePixelRatio'] = 'auto';
		$this->settings['rotateButton'] = false;
		
		$this->settings['label'] = Html::tag('p', Yii::t('media-slim', 'label_text'));
		$this->settings['labelLoading'] = Html::tag('p', Yii::t('media-slim', 'label_loading'));
		
		$this->settings['buttonEditLabel'] = Yii::t('media-slim', 'button_edit_label');
		$this->settings['buttonRemoveLabel'] = Yii::t('media-slim', 'button_remove_label');
		$this->settings['buttonDownloadLabel'] = Yii::t('media-slim', 'button_download_label');
		$this->settings['buttonUploadLabel'] = Yii::t('media-slim', 'button_upload_label');
		$this->settings['buttonCancelLabel'] = Yii::t('media-slim', 'button_cancel_label');
		$this->settings['buttonConfirmLabel'] = Yii::t('media-slim', 'button_confirm_label');
		
		$this->settings['buttonEditTitle'] = Yii::t('media-slim', 'button_edit_title');
		$this->settings['buttonRemoveTitle'] = Yii::t('media-slim', 'button_remove_title');
		$this->settings['buttonDownloadTitle'] = Yii::t('media-slim', 'button_download_title');
		$this->settings['buttonUploadTitle'] = Yii::t('media-slim', 'button_upload_title');
		$this->settings['buttonCancelTitle'] = Yii::t('media-slim', 'button_cancel_title');
		$this->settings['buttonConfirmTitle'] = Yii::t('media-slim', 'button_confirm_title');
		
		$this->settings['buttonConfirmClassName'] = 'btn btn-primary';
		$this->settings['buttonCancelClassName'] = 'btn btn-primary';
		
		
		$this->settings['statusFileType'] = Html::tag('p', Yii::t('media-slim', 'status_file_type'));
		$this->settings['statusFileSize'] = Html::tag('p', Yii::t('media-slim', 'status_file_size'));
		$this->settings['statusImageTooSmall'] = Html::tag('p', Yii::t('media-slim', 'status_file_small'));
		$this->settings['statusNoSupport'] = Html::tag('p', Yii::t('media-slim', 'status_crop_support'));
		$this->settings['statusContentLength'] = Html::tag('span', '', ['class' => 'slim-upload-status-icon']).' '.Yii::t('media-slim', 'status_file_big');
		$this->settings['statusUnknownResponse'] = Html::tag('span', '', ['class' => 'slim-upload-status-icon']).' '.Yii::t('media-slim', 'status_unknown');
		$this->settings['statusUploadSuccess'] = Html::tag('span', '', ['class' => 'slim-upload-status-icon']).' '.Yii::t('media-slim', 'status_saved');
		
		if (Yii::$app->user->getIsAdmin() && Yii::$app->user->getIsEditor())
			$this->settings['download'] = true;
		
		$deleteMessage = Yii::t('media-slim', 'confirm-delete');
		$deleteUrl = Url::toRoute('/media/default/delete');
		$deleteData = Json::encode(['media_hash' => $this->mediaHash]);
$js = <<<JS
	function(data, remove) {
		yii.confirm('$deleteMessage', function() {
			remove()
		    $.post('$deleteUrl', $deleteData);
		});
	}
JS;

		
		$this->settings['willRemove'] = new JsExpression($js);
	}
}