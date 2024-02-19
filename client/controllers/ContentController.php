<?php
namespace client\controllers;

use client\components\Controller;

use function Clue\StreamFilter\fun;
use common\modules\plugin\models\Plugin;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\vote\models\Vote;

use common\modules\tag\models\Tag;

use common\modules\user\models\User;

use common\modules\content\models\Content;
use common\modules\content\models\ContentStat;

/**
 * Class ContentController
 * @package client\controllers
 */
abstract class ContentController extends Controller
{
	/**
	 * @var string
	 */
	public $modelClass;
	
	/**
	 * @var string
	 */
	public $routeView;
	
	/**
 	 * @var string
	 */
	protected $modelName;
	
	/**
	 * @var array
	 */
	public $joinWith = [
		'media',
	];
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		$this->modelName = strtolower((new \ReflectionClass($this->modelClass))->getShortName());
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
						'actions' => ['index', 'popular', 'discussed', 'subscribed', 'view', 'iv'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all newest models.
	 * @param string $type
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex($type = 'newest') {
		
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = $this->modelClass::find()->joinWith(ArrayHelper::merge($this->joinWith, [
			'statistics',
			'stat',
			'tags',
			'company',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		]))->andWhere([
			$this->modelClass::tableName().'.status' => Status::ENABLED
		])->votes()->limit(10);
		
		if ($type == 'popular') {
			$query->orderBy([
				'pinned' => SORT_DESC,
				'contentVoteAggregate.positive' => SORT_DESC,
			]);
		}
		else if ($type == 'discussed') {
			$query->orderBy([
				'pinned' => SORT_DESC,
				ContentStat::tableName().'.comments' => SORT_DESC,
			]);
		}
		else if ($type == 'subscribed') {
			$tagsIds = ArrayHelper::getColumn(Tag::find()->votes()->voted(Vote::TAG_FAVORITE)->asArray()->all(), 'id');
			$authorIds = ArrayHelper::getColumn(User::find()->votes()->voted(Vote::USER_FAVORITE)->asArray()->all(), 'id');
			$query
				->andWhere([
					'or',
					['in', Tag::tableName().'.id', $tagsIds],
					['in', $this->modelClass::tableName().'.author_id', $authorIds]
				])
				->orderBy([
					'pinned' => SORT_DESC,
					'date_at' => SORT_DESC,
				]);
		}
		else {
			$query->orderBy([
				'pinned' => SORT_DESC,
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
			'type' => $type,
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
	 * Lists all subscribed models.
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionSubscribed() {
		return $this->actionIndex('subscribed');
	}
	
	/**
	 * Displays a single Content model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 * @throws \yii\db\Exception
	 */
	public function actionView($id) {
		$conditions = [];
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)
			$conditions['status'] = Status::ENABLED;
		
		/** @var Content $model */
		$model = $this->modelClass::findById($id, true, $this->modelName, [
			'author' => function($query) {
				$query->joinWith('profile')->votes();
			},
		], false, $conditions);
		
		// Set visit
		$model->setStat();
		
		// Find relevant articles by tags
		$tagsIds = $model->getTags_ids();
		
		$query = $this->modelClass::find()->joinWith([
			'media',
			'tags',
			'paymentTypes',
			'author' => function($query) {
				$query->joinWith('profile')->votes();
			},
			'tags',
			'company',
		])->andWhere([
			$this->modelClass::tableName().'.status' => Status::ENABLED
		])->limit(10);
		
		$query->andWhere([
			'in', Tag::tableName().'.id', $tagsIds
		])->andWhere([
			'<>', $this->modelClass::tableName().'.id', $model->id
		])->orderBy([
			'contentVoteRating' => SORT_DESC
		]);
		
		$query->votes();
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * @param $id
	 *
	 * @return \yii\web\Response
	 */
	public function actionIv($id) {
		$url = Url::toRoute([$this->routeView, 'id' => $id], true);
		$urlInstantView = 'https://t.me/iv?url='.$url.'&rhash=c36fdcf4f21bb7';
		return $this->redirect($urlInstantView, 301);
	}
	
}