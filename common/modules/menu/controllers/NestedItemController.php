<?php
namespace common\modules\menu\controllers;

use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\menu\models\Menu;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagNested;

use common\modules\menu\models\MenuItem;
use common\modules\menu\models\MenuNested;

class NestedItemController extends Controller
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
	 * @var integer
	 */
	public $nestedId;
	
	/**
	 * @var \common\modules\menu\models\MenuNested
	 */
	public $nestedModel;
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->menuId = Yii::$app->request->get('menu_id', Yii::$app->request->post('menu_id', 0));
		$this->menuModel = Menu::findOwn($this->menuId, true, 'menu');
		
		$this->nestedId = Yii::$app->request->get('nested_id', Yii::$app->request->post('nested_id', 0));
		$this->nestedModel = MenuNested::findOwn($this->nestedId, true, 'menu-item');
		
		return parent::beforeAction($action);
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['get'],
				],
			],
		];
	}
	
	
	/**
	 * Creates a new MenuItem model.
	 * If creation is successful, the browser will be redirected to the 'view' menu page.
	 *
	 * @return string|Response
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCreate() {
		
		// Create model
		$model = MenuItem::find()->where([
			'status' => Status::TEMP,
			'menu_id' => $this->menuId,
			'created_by' => Yii::$app->user->id,
		])->one();
		
		if (!$model) {
			
			// Create model
			$model = new MenuItem();
			$model->menu_id = $this->menuId;
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->sequence = 0;
			$model->save(false);
		}
		
		$model->sequence = MenuNested::lastSequence([
			'menu_id' => $this->menuId,
		]);
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			if (!MenuNested::find()->where('menu_id = :menu_id AND parent_id = :parent_id AND menu_item_id = :menu_item_id', [
				':menu_id' => $this->menuId,
				':parent_id' => $this->nestedId,
				':menu_item_id' => $model->id,
			])->count()) {
				
				$modelNested = new MenuNested();
				$modelNested->menu_id = $this->menuId;
				$modelNested->parent_id = $this->nestedId;
				$modelNested->menu_item_id = $model->id;
				if ($modelNested->appendTo($this->nestedModel)->save()) {
					
					// Set message
					Yii::$app->getSession()->setFlash('success', Yii::t('menu-item', 'message_create_success'));
					
					// Redirect to view
					return $this->redirect(['//menu/default/view', 'id' => $this->menuId]);
				}
			}
		}
		
		// Render view
		return $this->render('create', [
			'menu' => $this->menuModel,
			'model' => $model
		]);
	}
	
	/**
	 * Updates an existing MenuItem model.
	 * If update is successful, the browser will be redirected to the 'view' menu page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('menu-item', 'message_update_success'));
			
			// Redirect to view
			return $this->redirect(['//menu/default/view', 'id' => $this->menuId]);
		}
		
		// Render view
		return $this->render('update', [
			'menu' => $this->menuModel,
			'model' => $model,
		]);
	}
	
	/**
	 * Deletes an existing MenuItem and nesteds model.
	 * If deletion is successful, the browser will be redirected to the 'view' menu page.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionDelete($id) {
		
		// Load model
		$this->findModel($id, true)->delete();
		
		// Redirect to view
		return $this->redirect(['//menu/default/view', 'id' => $this->menuId]);
	}
	
	/**
	 * Finds the Menu model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return MenuItem the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id, $own = false) {
		return MenuItem::findBy($id, true, 'menu-item', [], false, $own);
	}
}