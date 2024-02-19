<?php
namespace client\controllers\user;

use common\modules\vote\models\Vote;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use common\modules\base\components\Controller;

use common\modules\content\helpers\enum\Status;

use common\modules\user\models\User;

class SubscribersController extends Controller
{
	/**
	 * @var integer
	 */
	public $moduleId;
	
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
						'actions' => ['index'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all user subscribers models.
	 * @param int $id
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex(int $id = 0) {
		if (!$id || ($id && ($id != Yii::$app->user->id && !Yii::$app->user->getIsAdmin())))
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		$query = User::find()->joinWith([
			'profile',
			'telegram',
			'address'
		])->subscribers(Vote::USER_FAVORITE, $id);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
}