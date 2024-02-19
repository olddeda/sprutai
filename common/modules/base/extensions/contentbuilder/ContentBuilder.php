<?php
namespace common\modules\base\extensions\contentbuilder;

use common\modules\base\components\ArrayHelper;
use common\modules\base\components\Debug;
use Yii;
use yii\bootstrap\InputWidget;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class ContentBuilder extends InputWidget
{
	/** @var array  */
	public $pluginOptions = [];
	
	private $_defaultPluginOptions = [
		'snippetFile' => 'basic.php',
		'snippetOpen' => true,
		'enlargeImage' => true,
		'toolbar' => 'top',
		'content' => '',
		//'customImageEditor' => 'contentBuilderImageSlim',
	];
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		$this->_defaultPluginOptions['snippetCategories'] = [
			[0, Yii::t('base-contentbuilder', 'category_default')],
			[-1, Yii::t('base-contentbuilder', 'category_all')],
			[10, Yii::t('base-contentbuilder', 'category_text')],
			[20, Yii::t('base-contentbuilder', 'category_image')],
			[30, Yii::t('base-contentbuilder', 'category_image_text')],
			[40, Yii::t('base-contentbuilder', 'category_slider')],
			[50, Yii::t('base-contentbuilder', 'category_video')],
			[60, Yii::t('base-contentbuilder', 'category_quote')],
			[70, Yii::t('base-contentbuilder', 'category_separator')],
			[80, Yii::t('base-contentbuilder', 'category_code')]
		];
		
		$this->_defaultPluginOptions['modulePath'] = Yii::getAlias('@web/contentbuilder/modules/');
		$this->_defaultPluginOptions['moduleConfig'] = [
			'moduleSaveImageHandler' => Yii::getAlias(Url::to(['/media/default/upload-content-builder-slider'])),
		];
		$this->_defaultPluginOptions['largerImageHandler'] = Yii::getAlias(Url::to(['/media/default/upload-content-builder-large']));
		
		
		$this->pluginOptions = ArrayHelper::merge($this->_defaultPluginOptions, $this->pluginOptions);
		
		$snippetFile = Yii::getAlias('@web/contentbuilder/snippets/'.$this->pluginOptions['snippetFile']);
		$snippetFileRoot = Yii::getAlias('@webroot/contentbuilder/snippets/'.$this->pluginOptions['snippetFile']);
		
		$this->pluginOptions['snippetFile'] = $snippetFile.'?'.filemtime($snippetFileRoot);
		$this->pluginOptions['onRender'] = new JsExpression('onContentBuilderRender');
		$this->pluginOptions['onDrop'] = new JsExpression('onContentBuilderDrop');
		$this->pluginOptions['pasteClean'] = !Yii::$app->user->getIsAdmin();
		
		if (!isset($this->pluginOptions['customval'])) {
			$customVal = base64_encode(serialize([
				'hash' => $this->model->hash,
				'module_type' => $this->model->getModuleType(),
			]));
			$this->pluginOptions['customval'] = $customVal;
		}
		
		$this->registerPlugin('contentbuilder');
		
		Html::addCssClass($this->options, 'form-control');
		
		$html = Html::beginTag('div', [
			'id' => 'content-builder-field',
			'class' => 'is-container container container-fluid contentbuilder-content',
		]);
		$html .= $this->pluginOptions['content'];
		$html .= Html::endTag('div');
		
		unset($this->options['id']);
		
		if ($this->hasModel()) {
			$html .= Html::activeHiddenInput($this->model, $this->attribute, $this->options);
		}
		else {
			$html .= Html::hiddenInput($this->name, $this->value, $this->options);
		}
		
		return $html;
	}
	
	/**
	 * @param $name
	 */
	protected function registerPlugin($name) {
		$view = $this->getView();
		
		ContentBuilderAsset::register($view);
		ContentBuilderContentAsset::register($view);
		ContentBuilderSlickAsset::register($view);
		//ContentBuilderSimpleLightBoxAsset::register($view);
		
		$id = $this->options['id'];
		
		$js = "jQuery('#content-builder-field').".$name."(".Json::encode($this->pluginOptions).");";
		$view->registerJs($js);
	}
}