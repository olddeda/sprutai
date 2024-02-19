<?php
namespace common\modules\queues\controllers;

use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Controller;

use common\modules\queues\Module;
use common\modules\queues\base\FlashTrait;
use common\modules\queues\filters\JobFilter;
use common\modules\queues\records\PushRecord;

/**
 * Class JobController
 * @package common\modules\queues\controllers
 */
class JobController extends Controller
{
    use FlashTrait;

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'push' => ['post'],
                    'stop' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Pushed jobs
     *
     * @return mixed
     */
    public function actionIndex() {
        return $this->render('index', [
            'filter' => JobFilter::ensure(),
        ]);
    }

    /**
     * Job view
	 * @param $id
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionView($id) {
        $record = $this->findRecord($id);
        if ($record->lastExec && $record->lastExec->isFailed()) {
            return $this->redirect(['view-attempts', 'id' => $record->id]);
        }
        return $this->redirect(['view-details', 'id' => $record->id]);
    }

    /**
     * Push details
     *
     * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionViewDetails($id) {
        return $this->render('view-details', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Push environment
     *
     * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionViewContext($id) {
        return $this->render('view-context', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Job object data
     *
     * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionViewData($id) {
        return $this->render('view-data', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Attempts
     *
     * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionViewAttempts($id) {
        return $this->render('view-attempts', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Pushes a job again
     *
     * @param int $id
	 *
	 * @return \yii\web\Response
	 * @throws ForbiddenHttpException
	 * @throws NotFoundHttpException
	 */
    public function actionPush($id) {
        if (!$this->module->canPushAgain) {
            throw new ForbiddenHttpException('Push is forbidden.');
        }

        $record = $this->findRecord($id);

        if (!$record->isSenderValid()) {
            return $this
                ->error("The job isn't pushed because $record->sender_name component isn't found.")
                ->redirect(['view-details', 'id' => $record->id]);
        }

        if (!$record->isJobValid()) {
            return $this
                ->error('The job isn\'t pushed because it must be JobInterface instance.')
                ->redirect(['view-data', 'id' => $record->id]);
        }

        $uid = $record->getSender()->push($record->createJob());
        $newRecord = PushRecord::find()->byJob($record->sender_name, $uid)->one();

        return $this
            ->success('The job is pushed again.')
            ->redirect(['view', 'id' => $newRecord->id]);
    }

    /**
     * Stop a job
     *
     * @param int $id
     * @throws
     * @return mixed
     */
    public function actionStop($id) {
        if (!$this->module->canExecStop) {
            throw new ForbiddenHttpException('Stop is forbidden.');
        }

        $record = $this->findRecord($id);

        if ($record->isStopped()) {
            return $this
                ->error('The job is already stopped.')
                ->redirect(['view-details', 'id' => $record->id]);
        }

        if (!$record->canStop()) {
            return $this
                ->error('The job is already done.')
                ->redirect(['view-attempts', 'id' => $record->id]);
        }

        $record->stop();

        return $this
            ->success('The job will be stopped.')
            ->redirect(['view-details', 'id' => $record->id]);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @return PushRecord
     */
    protected function findRecord($id) {
        if ($record = PushRecord::find()->byId($id)->one()) {
            return $record;
        }
        throw new NotFoundHttpException('Record not found.');
    }
}
