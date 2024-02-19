<?php
namespace common\modules\menu\controllers;

use Yii;
use yii\web\Response;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\menu\models\Menu;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagNested;

class NestedController extends Controller
{
	/**
	 * @var integer
	 */
	public $menuId;
	
	/**
	 * @var \common\modules\menu\models\Menu
	 */
	public $menuModel;
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->menuId = Yii::$app->request->get('menu_id', 0);
		$this->menuModel = Menu::findOwn($this->menuId, true, 'menu');
		
		return parent::beforeAction($action);
	}
	
	public function actionCreate() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$nestedId = Yii::$app->request->get('nested_id');
		$tagId = Yii::$app->request->get('tag_id');
		
		$result = [
			'success' => false,
		];
		
		if (TagNested::find()->where('module_type = :module_type AND module_id = :module_id AND parent_id = :parent_id AND tag_id = :tag_id', [
			':module_type' => $this->menuModel->getModuleType(),
			':module_id' => $this->menuId,
			':parent_id' => $nestedId,
			':tag_id' => $tagId,
		])->count()) {
			$result['error'] = Yii::t('menu-nested', 'error_link_exists');
			return $result;
		}
		
		/** @var \common\modules\tag\models\TagNested $nestedModel */
		$nestedModel = TagNested::findById($nestedId, false, 'tag-nested');
		if (is_null($nestedModel)) {
			$result['error'] = Yii::t('menu-nested', 'error_link_parent_not_found');
			return $result;
		}
		
		/** @var \common\modules\tag\models\Tag $tagModel */
		$tagModel = Tag::findById($tagId, false, 'tag');
		if (is_null($nestedModel)) {
			$result['error'] = Yii::t('menu-nested', 'error_link_child_not_found');
			return $result;
		}
		
		if ($nestedModel->tag_id == $tagModel->id) {
			$result['error'] = Yii::t('menu-nested', 'error_link_same');
			return $result;
		}
		
		$model = new TagNested();
		$model->module_type = $this->menuModel->getModuleType();
		$model->module_id = $this->menuId;
		$model->parent_id = $nestedModel->id;
		$model->tag_id = $tagModel->id;
		if ($model->appendTo($nestedModel)->save()) {
			$result['success'] = true;
			$result['item'] = [
				'id' => $model->id,
				'title' => $model->tag->title,
				'tag_id' => $model->tag->id,
				'level' => $model->depth,
			];
		}
		else
			$result['error'] = current($model->errors);
		
		return $result;
	}
	
	public function actionUpdate() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$nestedId = Yii::$app->request->get('nested_id');
		$tagId = Yii::$app->request->get('tag_id');
		
		$result = [
			'success' => false,
		];
		
		if (TagNested::find()->where('module_type = :module_type AND module_id = :module_id AND parent_id = :parent_id AND tag_id = :tag_id', [
			':module_type' => $this->menuModel->getModuleType(),
			':module_id' => $this->menuId,
			':parent_id' => $nestedId,
			':tag_id' => $tagId,
		])->count()) {
			$result['error'] = Yii::t('menu-nested', 'error_link_exists');
			return $result;
		}
		
		/** @var \common\modules\tag\models\TagNested $nestedModel */
		$nestedModel = TagNested::findById($nestedId, false, 'tag-nested');
		if (is_null($nestedModel)) {
			$result['error'] = Yii::t('menu-nested', 'error_link_parent_not_found');
			return $result;
		}
		
		/** @var \common\modules\tag\models\Tag $tagModel */
		$tagModel = Tag::findById($tagId, false, 'tag');
		if (is_null($nestedModel)) {
			$result['error'] = Yii::t('menu-nested', 'error_link_child_not_found');
			return $result;
		}
		
		if ($nestedModel->tag_id == $tagModel->id) {
			$result['error'] = Yii::t('menu-nested', 'error_link_same');
			return $result;
		}
		
		$nestedModel->tag_id = $tagModel->id;
		
		if ($nestedModel->save()) {
			$result['success'] = true;
			$result['item'] = [
				'id' => $nestedModel->id,
				'title' => $nestedModel->tag->title,
			];
		}
		else
			$result['error'] = current($nestedModel->errors);
		
		return $result;
	}
	
	public function actionDelete() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$result = [
			'success' => false,
		];
		
		$ids = Yii::$app->request->post('ids');
		if (is_array($ids) && count($ids)) {
			TagNested::deleteAll(['in', 'id', $ids]);
			$result['success'] = true;
		}
		
		return $result;
	}
}