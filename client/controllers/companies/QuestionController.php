<?php
namespace client\controllers\companies;

use client\components\Controller;

use common\modules\base\components\Debug;
use common\modules\content\models\ContentStat;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use common\modules\content\models\Question;
use common\modules\content\helpers\enum\Status;

use common\modules\company\models\Company;

class QuestionController extends Controller
{
	/**
	 * @var integer
	 */
	public $companyId;
	
	/**
	 * @var \common\modules\company\models\Company
	 */
	public $companyModel;
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->companyId = Yii::$app->request->get('company_id', 0);
		$this->companyModel = Company::findById($this->companyId, true, 'company');
		
		return parent::beforeAction($action);
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'popular', 'discussed', 'view'],
						'roles' => ['?', '@'],
					],
					[
						'allow' => true,
						'actions' => ['create', 'update', 'delete'],
						'roles' => ['@'],
					],
				],
			],
		]);
	}
	
	/**
	 * List all questions company
	 * @return string
	 */
	public function actionIndex($type = 'newest') {
		
		/** @var \common\modules\company\models\query\CompanyQuery $query */
		$query = Question::find()->joinWith([
		    'stat',
        ])->andWhere([
			Question::tableName().'.company_id' => $this->companyId,
			Question::tableName().'.status' => Status::ENABLED,
		])->votes();

        if ($type == 'popular') {
            $query->orderBy([
                'contentVoteAggregate.positive' => SORT_DESC,
            ]);
        }
        else if ($type == 'discussed') {
            $query->orderBy([
                ContentStat::tableName().'.comments' => SORT_DESC,
            ]);
        }
        else {
            $query->orderBy([
                'date_at' => SORT_DESC,
            ]);
        }
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'company' => $this->companyModel,
		]);
	}

    /**
     * Lists all popular models.
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPopular() {
        return $this->actionIndex('popular');
    }

    /**
     * Lists all discussed models.
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDiscussed() {
        return $this->actionIndex('discussed');
    }

    /**
     * Displays a single Question model.
     *
     * @param integer $id
     *
     * @return string
     * @throws \yii\db\Exception
     */
	public function actionView($id) {
		
		// Find model
		$model = $this->findModel($id);
		
		// Set visit
		$model->setStat();
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Creates a new Question model.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		
		// Create model
		$model = Question::find()->where([
			'type' => Question::type(),
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
			'company_id' => $this->companyId,
		])->one();
		if (!$model) {
			$model = new Question();
			$model->type = Question::type();
			$model->status = Status::TEMP;
			$model->created_by = Yii::$app->user->id;
			$model->company_id = $this->companyId;
			$model->save(false);
		}
		$model->status = Status::ENABLED;
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('company-question', 'message_create_success'));
				
				// Redirect to view
				return $this->redirect(['view', 'company_id' => $this->companyId, 'id' => $model->id]);
			}
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Updates an existing Question model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 * @throws \yii\base\ExitException
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if ($model->validate() && $model->save()) {
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('company-question', 'message_update_success'));
				
				// Redirect to view
				return $this->redirect(['view', 'company_id' => $this->companyId, 'id' => $model->id]);
			}
			
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'company' => $this->companyModel,
		]);
	}
	
	/**
	 * Delete question model
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete($id) {
		
		// Find and delete model
		$this->findModel($id, true)->delete();
		
		// Redirect to index
		return $this->redirect(['index', 'company_id' => $this->companyId]);
	}
	
	/**
	 * Finds the Question model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool $own
	 *
	 * @return array|null|\yii\db\ActiveRecord
	 */
	protected function findModel($id, $own = false) {
		return Question::findBy($id, true, 'company-question', [], false, $own);
	}
}