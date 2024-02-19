<?php
namespace common\modules\telegram\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Controller;

use common\modules\telegram\models\TelegramUser;
use common\modules\telegram\models\TelegramProject;
use common\modules\telegram\models\TelegramRequest;
use common\modules\telegram\models\TelegramRequestUser;
use common\modules\telegram\models\search\TelegramUserSearch;
use common\modules\telegram\models\search\TelegramRequestSearch;
use common\modules\telegram\models\search\TelegramRequestUserSearch;
use common\modules\telegram\models\search\TelegramProjectSearch;

use common\modules\telegram\helpers\enum\Status;

/**
 * UserController implements the CRUD actions for TelegramUser model.
 */
class UserController extends Controller
{
    /**
     * Lists all TelegramUser models.
     * @return mixed
     */
    public function actionIndex()  {
        $searchModel = new TelegramUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	/**
	 * Displays a single TelegramUser model.
	 * @param $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
	
	/**
	 * Block user
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionBlock($id) {
		$this->findModel($id)->block();
		
		TelegramRequest::updateAll([
			'status' => Status::DELETED_PREPARE,
		], [
			'telegram_user_id' => $id,
			'status' => Status::ENABLED,
		]);
		
		return $this->redirect(['index']);
	}
	
	/**
	 * Unblock user
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionUnblock($id) {
		$this->findModel($id)->unblock();
		
		TelegramRequest::updateAll([
			'status' => Status::DELETED_PREPARE,
		], [
			'telegram_user_id' => $id,
			'status' => Status::ENABLED,
		]);
		
		return $this->redirect(['index']);
	}
	
	/**
	 * Display user requests
	 * @param $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionRequest($id) {
	
		$searchModel = new TelegramRequestSearch();
		
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([TelegramRequest::tableName().'.telegram_user_id' => $id]);
		
  
		return $this->render('request', [
			'model' => $this->findModel($id),
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}
	
	/**
	 * Display user requests answers
	 * @param $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionAnswer($id) {
		
		$searchModel = new TelegramRequestUserSearch();
		
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([TelegramRequestUser::tableName().'.telegram_user_id' => $id]);
		
		return $this->render('answer', [
			'model' => $this->findModel($id),
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}
	
	/**
	 * Display user projects
	 * @param $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionProject($id) {
		$searchModel = new TelegramProjectSearch();
		
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([TelegramProject::tableName().'.telegram_user_id' => $id]);
		
		return $this->render('project', [
			'model' => $this->findModel($id),
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

    /**
     * Finds the TelegramUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TelegramUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = TelegramUser::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('telegram-user', 'The requested page does not exist.'));
    }
}
