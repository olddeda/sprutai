<?php
namespace common\modules\shortener\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\MethodNotAllowedHttpException;

use common\modules\rbac\components\AccessControl;

use common\modules\base\extensions\editable\EditableAction;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\shortener\models\Shortener;
use common\modules\shortener\models\search\ShortenerSearch;
use common\modules\shortener\models\search\ShortenerHitSearch;

/**
 * DefaultController implements the CRUD actions for Banner model.
 */
class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['redirect'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Shortener::class,
				'forceCreate' => false
			],
		]);
	}
	
	/**
	 * Lists all Banner models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ShortenerSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Banner model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 */
	public function actionView($id) {
        
        $searchModel = new ShortenerHitSearch();
        $searchModel->link_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		// Render view
		return $this->render('view', [
			'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new Banner model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
        $model = new Shortener();
        $model->status = Status::ENABLED;
        $model->created_by = Yii::$app->user->id;
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('shortener', 'message_create_success'));

			// Redirect to view
			return $this->redirect(['index']);
		}
        
        // Render view
        return $this->render('create', [
            'model' => $model,
        ]);
	}

	/**
	 * Updates an existing Banner model.
	 * If update is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionUpdate($id) {

		// Load model
		$model = $this->findModel($id, true);

		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {

			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('shortener', 'message_update_success'));

			// Redirect to view
			return $this->redirect(['index']);
		}
        
        // Render view
        return $this->render('update', [
            'model' => $model,
        ]);
	}

	/**
	 * Deletes an existing Banner model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return \yii\web\Response
	 */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();

		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('shortener', 'message_delete_success'));

		// Redirect to index
		return $this->redirect(['index']);
	}
    
    /**
     * @param $hash
     * @return \yii\web\Response
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionRedirect($hash) {
        $link = $this->findModelByHash($hash);
        
        $ip = Yii::$app->request->userIP;
        $user_agent = Yii::$app->request->userAgent;
        
        if ($link->isActive() && $link->generateHit($ip, $user_agent)) {
            $link->updateCounter();
            return $this->redirect($link->url);
        }
        
        return $this->redirect('site/error');
    }

	/**
	 * Finds the Banner model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return Shortener the loaded model
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		return Shortener::findBy($id, true, 'shortener', [], false, $own);
	}
    
    /**
     * Finds the Link model based on its hash code value.
     *
     * @param $hash
     * @return Link
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    protected function findModelByHash($hash) {
        if (($model = Shortener::findByHash($hash)) !== null) {
            if ($model->isActive()) {
                return $model;
            }
            
            throw new MethodNotAllowedHttpException('Истек срок действия ссылки.');
        }
        throw new NotFoundHttpException('Запрашиваемая ссылка не существует.');
    }
}
