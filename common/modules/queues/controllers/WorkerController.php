<?php
namespace common\modules\queues\controllers;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\queues\Env;
use common\modules\queues\Module;
use common\modules\queues\base\FlashTrait;
use common\modules\queues\filters\WorkerFilter;
use common\modules\queues\records\WorkerRecord;

/**
 * Class WorkerController
 * @package common\modules\queues\controllers
 */
class WorkerController extends Controller
{
    use FlashTrait;

    /**
     * @var Module
     */
    public $module;
    
    /**
     * @var Env
     */
    protected $env;

    public function __construct($id, $module, Env $env, array $config = []) {
        $this->env = $env;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'stop' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Worker List
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('index', [
            'filter' => WorkerFilter::ensure(),
        ]);
    }

    /**
     * Stops a worker
     *
     * @param int $id
	 *
	 * @return \yii\web\Response
	 * @throws ForbiddenHttpException
	 * @throws NotFoundHttpException
	 */
    public function actionStop($id) {
        if (!$this->module->canWorkerStop) {
            throw new ForbiddenHttpException('Stop is forbidden.');
        }

        $record = $this->findRecord($id);
        $record->stop();
        return $this->success(strtr('The worker will be stopped within {timeout} sec.', [
        	'{timeout}' => $record->pinged_at + $this->env->workerPingInterval - time(),
        ]))->redirect(['index']);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @return WorkerRecord
     */
    protected function findRecord($id) {
        if ($record = WorkerRecord::findOne($id)) {
            return $record;
        }
        throw new NotFoundHttpException('Record not found.');
    }
}
