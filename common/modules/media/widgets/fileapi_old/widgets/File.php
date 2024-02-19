<?php

namespace common\modules\media\widgets\fileapi\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

use common\modules\media\Manager;
use common\modules\media\models\Media;
use common\modules\media\behaviors\MediaBehavior;

use common\modules\media\widgets\fileapi\Widget;
use common\modules\media\widgets\fileapi\Asset;
use common\modules\media\widgets\fileapi\assets\FileAsset;

class File extends Widget
{
	/**
	 * @var url
	 */
	public $url;

	/**
	 * @var string FileAPI selector
	 */
	public $selector;

	/**
	 * @var array
	 */
	protected $defaultSettings = [
		'autoUpload' => false
	];

	/**
	 * Widget settings.
	 *
	 * @var array {@link https://github.com/RubaXa/jquery.fileapi/ FileAPI options}
	 */
	public $settings = [];

	/**
	 * @var string Widget template view
	 *
	 * @see \yii\base\Widget::render
	 */
	public $template;

	public $crop;

	protected $_attachments = [];

	protected $_multiple;

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		$this->settings = ArrayHelper::merge($this->defaultSettings, $this->settings);
		$this->settings['selector'] = $this->getSelector();

		//$this->checkBehavior();
		//$this->checkMultiple();

		$this->setupCsrf();
		$this->setupUrl();
		//$this->setupAttachments();
		$this->setupTemplate();
	}

	/**
	 * Check behavior
	 * @throws InvalidConfigException
	 */
	public function checkBehavior() {

		/** @var MediaBehavior $behavior */
		$behavior = $this->getMediaBehavior();

		$class = MediaBehavior::className();
		$name = MediaBehavior::NAME;

		if (!$behavior) {
			throw new InvalidConfigException('Behavior "'.$class.'" with name "'.$name.'" does not exists in model');
		}
	}

	/**
	 * Check multiple
	 */
	public function checkMultiple() {
		$config = $this->getModelAttachmentConfig();
		if ($config) {
			$this->setMultiple($config['multiple']);
			$this->settings['multiple'] = $config['multiple'];
		}
	}

	/**
	 * Setup csrf
	 */
	public function setupCsrf() {
		$request = Yii::$app->getRequest();

		if ($request->enableCsrfValidation === true) {
			$this->settings['data'][$request->csrfParam] = $request->getCsrfToken();
		}
	}

	/**
	 * Setup url
	 */
	public function setupUrl() {
		$request = Yii::$app->getRequest();

		if (!isset($this->settings['url'])) {
			$this->settings['url'] = $this->url ? Url::to($this->url) : $request->getUrl();
		}
		else {
			$this->settings['url'] = Url::to($this->settings['url']);
		}
	}

	/**
	 * Setup attachments
	 */
	public function setupAttachments() {
		$related = $this->getModelAttributeValue();

		$this->_attachments = is_array($related) ? $related : [$related];
	}

	/**
	 * Setup template
	 */
	public function setupTemplate() {
		if ($this->template === null) {
			$this->template = $this->isMultiple() ? 'file_multiple' : 'file_single';
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run() {
		$this->registerFiles();
		$this->register();

		$data = [
			'selector' => $this->getSelector(),
			'settings' => $this->settings,
			'value' => $this->value,
			'crop' => $this->crop,
			'paramName' => Manager::PARAM_NAME,
			'inputName' => $this->getHiddenInputName(),
		];

		return $this->render($this->template, $data);
	}

	/**
	 * Registering already uploaded files.
	 */
	public function registerFiles() {
		foreach ($this->_attachments as $attach) {
			if ($attach) {
				$this->settings['files'][] = [
					'id' => $attach->id,
					'src' => $attach->fileUrl,
					'name' => $attach->name,
					'type' => $attach->mime
				];
			}
		}
	}

	/**
	 * Register all widget scripts and callbacks
	 */
	public function register() {
		$this->registerMainClientScript();
		$this->registerClientScript();
	}

	/**
	 * Register widget main asset.
	 */
	protected function registerMainClientScript() {
		$view = $this->getView();
		Asset::register($view);
	}

	/**
	 * Register widget asset.
	 */
	public function registerClientScript() {
		$view = $this->getView();

		FileAsset::register($view);

		$selector = $this->getSelector();
		$options = Json::encode($this->settings);

		$view->registerJs("jQuery('#$selector').yiiMediaFileAPI('file', $options);");
	}

	/**
	 * @return bool
	 */
	public function isMultiple() {
		return $this->_multiple;
	}

	/**
	 * @param $value
	 */
	public function setMultiple($value) {
		$this->_multiple = $value;
		$this->settings['multiple'] = $value;
	}

	/**
	 * @return string Widget selector
	 */
	public function getSelector() {
		return $this->selector !== null ? $this->selector : 'media-'.$this->options['id'];
	}

	/**
	 * @return MediaBehavior
	 */
	protected function getMediaBehavior() {
		return $this->model->getBehavior(MediaBehavior::NAME);
	}

	/**
	 * @return array
	 */
	protected function getModelAttachmentConfig() {
		return $this->getMediaBehavior()->getAttachConfig($this->attribute);
	}

	/**
	 * @return Media
	 */
	protected function getModelMedia() {
		return $this->getMediaBehavior()->getAttach($this->attribute);
	}

	/**
	 * @return mixed
	 */
	protected function getModelAttributeValue() {
		return $this->model->{$this->attribute};
	}

	/**
	 * @return string
	 */
	protected function getHiddenInputName() {
		return $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
	}

	/**
	 * Get hidden input
	 * @return string
	 */
	protected function getHiddenInput() {
		return $this->hasModel() ? Html::activeHiddenInput($this->model, $this->attribute, $this->options) : Html::hiddenInput($this->name, $this->value, $this->options);
	}
}
