<?php
namespace common\modules\media\widgets\fileinput;

use Yii;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\base\extensions\fileinput\FileInput;

use common\modules\media\models\MediaPlaceholder;

class ImageWidget extends InputWidget
{
	/** @var bool multiple upload  */
	public $multiple = false;
	
	/** @var integer */
	public $mediaType;
	
	/** @var integer */
	public $width;
	
	/** @var integer */
	public $height;
	
	/** @var integer */
	public $format;
	
	/** @var bool */
	public $fileInputPluginLoading = true;
	
	/** @var array */
	public $fileInputPluginOptions = [];
	
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		// Render modal
		$view = $this->getView();
		$view->on($view::EVENT_END_BODY, function ($event) {
			echo $this->render('modal');
		});
	}
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		$this->registerClientScripts();
		
		$images = $this->model->getImages();
		$ul = Html::ul($images, [
			'item' => function ($item) {
				return self::row($item, $this->width, $this->height, $this->format);
			},
			'class' => 'fileinput-image-widget'
		]);
		
		return Html::tag('div', $ul.$this->getFileInput());
	}
	
	/**
	 * Register widget asset.
	 */
	protected function registerClientScripts() {
		$view = $this->getView();
		ImageWidgetAsset::register($view);
	}
	
	/**
	 * Get file input
	 * @return string
	 */
	private function getFileInput() {
		return FileInput::widget([
			'name' => $this->attribute.'[]',
			'options' => [
				'accept' => 'image/*',
				'multiple' => $this->multiple,
			],
			'showMessage' => false,
			'pluginOptions' => $this->fileInputPluginOptions,
			'pluginLoading' => $this->fileInputPluginLoading,
			'pluginEvents' => [
				'fileselect' => 'appmake.fileinput_image_widget.uploadSelect',
				'fileuploaded' => 'appmake.fileinput_image_widget.uploadSuccess',
			]
		]);
	}
	
	/**
	 * Get row
	 * @param $image
	 *
	 * @return string
	 */
	public static function row($image, $width, $height, $format) {
		if (is_null($image) || $image instanceof MediaPlaceholder)
			return '';
		
		$class = ' fileinput-image-widget-row';
		
		$liParams = self::_getParams($image);
		$liParams['class'] .= $class;
		
		return Html::tag('li', self::_getImagePreview($image, $width, $height, $format), $liParams);
	}
	
	/**
	 * Get params
	 * @param $id
	 *
	 * @return array
	 */
	private static function _getParams($image) {
		return [
			'class' => 'fileinput-image-widget-item',
			'data-media_hash' => $image->hash,
		];
	}
	
	/**
	 * Get image preview
	 * @param $image
	 *
	 * @return string
	 */
	private static function _getImagePreview($image, $width, $height, $format) {
		$update = Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', '#', [
			'data-action' => Url::toRoute(['/media/default/modal']),
			'class' => 'update'
		]);
		$delete = Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', '#', [
			'data-action' => Url::toRoute(['/media/default/delete']),
			'data-message' => Yii::t('media-image', 'confirm_delete'),
			'data-toggle' => 'tooltip',
			'class' => 'delete'
		]);
		$img = Html::img($image->getImageSrc($width, $height, $format), [
			'data-action' => Url::toRoute(['/media/default/set-main']),
			'width' => $width,
			'height' => $height,
			'class' => 'thumb',
			'style' => 'width:'.$width.'px; height:'.$height.'px',
		]);
		$a = $img;
		
		return $update.$delete.$a;
	}
	
}