<?php
namespace common\modules\base\components;

use Yii;
use yii\filters\VerbFilter;
use yii\base\Model;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\modules\rbac\components\AccessControl;

class Controller extends \yii\web\Controller
{
	/**
	 * @var string the name of the layout content to be applied to this controller's views.
	 */
	public $layoutContent = 'content';
	
	/**
	 * @var string the name of the layout sidebar to be applied to this controller's views.
	 */
	public $layoutSidebar = 'sidebar';
	
	/**
	 * @var array params for layout sidebar
	 */
	public $layoutSidebarParams = [];
	
	/**
	 * @var string
	 */
	public $bodyClass = '';
	
	/**
	 * @var bool
	 */
	public $rememberUrl = true;

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
			    'class' => AccessControl::class,
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['post'],
					'editable' => ['get','post'],
				],
			],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function runAction($id, $params = []) {
		if ($this->rememberUrl)
			Url::remember();
		
		return parent::runAction($id, $params);
	}
	
	/**
	 * Performs ajax validation.
	 *
	 * @param Model $model
	 *
	 * @throws \yii\base\ExitException
	 */
	protected function performAjaxValidation(Model $model) {
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			echo json_encode(ActiveForm::validate($model));
			exit();
		}
	}
}

