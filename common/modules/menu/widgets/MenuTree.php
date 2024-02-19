<?php
namespace common\modules\menu\widgets;

use common\modules\base\components\Debug;
use Yii;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

use common\modules\menu\assets\TreeAsset;
use yii\web\JsExpression;

class MenuTree extends Widget
{
	/**
	 * @var array
	 */
	public $items = [];
	
	/**
	 * @var array
	 */
	public $itemOptions = [];
	
	/**
	 * @var string
	 */
	public $menuRootTemplate = '<div id="{id}" tag="{tag}" level="{level}" class="node">{title}</div>';
	
	/**
	 * @var string
	 */
	public $menuTemplate = '<div id="{id}" tag="{tag}" level="{level}" class="node">{title}</div>';
	
	/**
	 * @var string the template used to render a list of sub-menus.
	 * In this template, the token `{items}` will be replaced with the rendered sub-menu items.
	 */
	public $submenuTemplate = "\n<ul>\n{items}\n</ul>\n";
	
	/**
	 * @var array
	 */
	public $options = [];
	
	/**
	 * @var bool
	 */
	public $addOption = true;
	
	/**
	 * @var bool
	 */
	public $editOption = true;
	
	/**
	 * @var bool
	 */
	public $deleteOption = true;
	
	/**
	 * @var bool
	 */
	public $viewOption = false;
	
	/**
	 * @var bool
	 */
	public $fullWidthOption = false;
	
	/**
	 * @var string
	 */
	public $urlSearch;
	
	/**
	 * @var string
	 */
	public $urlCreate;
	
	/**
	 * @var string
	 */
	public $urlUpdate;
	
	/**
	 * @var string
	 */
	public $urlDelete;
	
	/**
	 * @var array
	 */
	public $callbacks = [
		'create' => 0,
		'update' => 0,
		'delete' => 0
	];
	
	/**
	 * Renders the menu.
	 */
	public function run() {
		
		$items = $this->normalizeItems($this->items);
		
		if (!empty($items)) {
			$options = $this->options;
			$tag = ArrayHelper::remove($options, 'tag', 'ul');
			
			echo Html::tag($tag, $this->renderItems($items, true), $options);
		}
		
		$this->registerAssets();
	}
	
	protected function registerAssets() {
		TreeAsset::register($this->view);
		
		$options = [
			'add_option' => $this->addOption,
			'edit_option' => $this->editOption,
			'delete_option' => $this->deleteOption,
			'view_option' => $this->viewOption,
			'draggableOption' => false,
			'confirm_before_delete' => true,
			'animate_option' => [false, 5],
			'fullwidth_option' => $this->fullWidthOption,
			'align_option' => 'top',
			'url' => [
				'search' => $this->urlSearch,
				'create' => $this->urlCreate,
				'update' => $this->urlUpdate,
				'delete' => $this->urlDelete,
			],
			'callbacks' => $this->callbacks
		];
		
		$optionsJson = Json::encode($options);
		
		$js = <<<JS
$('.tree').tree($optionsJson);
JS;
		$this->view->registerJs($js);
	}
	
	/**
	 * Recursively renders the menu items (without the container tag).
	 * @param array $items the menu items to be rendered recursively
	 * @return string the rendering result
	 */
	protected function renderItems($items, $isRoot = false) {
		$n = count($items);
		$lines = [];
		
		foreach ($items as $i => $item) {
			$options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
			$tag = ArrayHelper::remove($options, 'tag', 'li');
			$class = [];
			Html::addCssClass($options, $class);
			
			$menu = $this->renderItem($item, $isRoot);
			$isRoot = false;
			if (!empty($item['items'])) {
				$submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
				$menu .= strtr($submenuTemplate, [
					'{items}' => $this->renderItems($item['items'], $isRoot),
				]);
			}
			$lines[] = Html::tag($tag, $menu, $options);
		}
		
		return implode("\n", $lines);
	}
	
	/**
	 * Renders the content of a menu item.
	 * Note that the container and the sub-menus are not rendered here.
	 * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
	 * @return string the rendering result
	 */
	protected function renderItem($item, $isRoot = false) {
		$template = ArrayHelper::getValue($item, 'template', ($isRoot ? $this->menuRootTemplate : $this->menuTemplate));
		
		return strtr($template, [
			'{id}' => $item['nested_id'],
			'{title}' => $item['title'],
			'{url}' => $item['url'],
			'{tag}' => $item['id'],
			'{level}' => $item['level'],
			'{descr}' => $item['descr'],
			'{image}' => $item['image'],
			'{classLink}' => $item['classLink'],
			'{classNoLink}' => $item['classNoLink']
		]);
	}
	
	/**
	 * Normalizes the [[items]] property to remove invisible items and activate certain items.
	 * @param array $items the items to be normalized.
	 * @return array the normalized menu items
	 */
	protected function normalizeItems($items) {
		foreach ($items as $i => $item) {
			if (!isset($item['id'])) {
				$item['id'] = 0;
			}
			if (!isset($item['level'])) {
				$item['level'] = 0;
			}
			if (!isset($item['tag'])) {
				$item['tag'] = 0;
			}
			if (!isset($item['title'])) {
				$item['title'] = '';
			}
			if (!isset($item['url'])) {
				$item['url'] = '';
			}
			if (!isset($item['descr'])) {
				$item['descr'] = '';
			}
			if (!isset($item['image'])) {
				$item['image'] = '';
			}
			$item['classLink'] = (strlen($item['url'])) ? 'show' : 'hide';
			$item['classNoLink'] = (strlen($item['url'])) ? 'hide' : 'show';
			
			$items[$i] = $item;
			
			if (isset($item['items'])) {
				$items[$i]['items'] = $this->normalizeItems($item['items']);
			}
		}
		
		return array_values($items);
	}
}