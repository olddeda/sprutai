<?php
namespace client\controllers\companies;

use client\components\Controller;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\content\models\Content;
use common\modules\content\models\Article;
use common\modules\content\models\Blog;
use common\modules\content\models\News;
use common\modules\content\models\Portfolio;
use common\modules\content\helpers\enum\Type as ContentType;

use common\modules\tag\models\Tag;

use common\modules\vote\models\Vote;

use common\modules\user\models\User;

use common\modules\company\models\Company;
use common\modules\company\helpers\enum\Type;

class DefaultController extends Controller
{
	
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
						'actions' => ['index', 'vendors', 'integrators', 'shops', 'view', 'articles', 'news', 'blogs', 'portfolio', 'mentions', 'subscribers'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all vendors
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex($type = null) {
		$typeId = Type::VENDOR;
		if ($type == 'integrator')
			$typeId = Type::INTEGRATOR;
		else if ($type == 'shop')
			$typeId = Type::SHOP;
		
		/** @var \common\modules\company\models\query\CompanyQuery $query */
		$query = Company::find()->joinWith([
			'media',
			'tags',
			'address',
			'contentsStat',
			'discounts'
		])->andWhere([
			Company::tableName().'.status' => Status::ENABLED,
		])->votes();
		
		if (!is_null($type)) {
			$query->andWhere('('.Company::tableName().'.type & :type) = :type', [
				'type' => $typeId,
			]);
		}
		
		$query->orderBy([
			'companyFavoriteAggregate.positive' => SORT_DESC,
		]);
		
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
	
	public function actionVendors() {
		return $this->actionIndex('vendor');
	}
	
	/**
	 * Lists all integrators
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIntegrators() {
		return $this->actionIndex('integrator');
	}
	
	
	/**
	 * Lists all shops
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionShops() {
		return $this->actionIndex('shop');
	}
	
	/**
	 * Lists all mentions
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionMentions($id) {
		
		// Load model
		$model = $this->loadModel($id);
		
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = Content::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'tags',
			'company',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->where([
			Content::tableName().'.status' => Status::ENABLED,
			Tag::tableName().'.id' => $model->tag_id,
		])->andWhere(Tag::tableName().'.id IS NOT NULL')->andWhere([
			'in',
			Content::tableName().'.type',
			[ContentType::NEWS, ContentType::ARTICLE, ContentType::BLOG],
		])->orderBy([
			'date_at' => SORT_DESC,
		])->votes();
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('mentions', [
			'dataProvider' => $dataProvider,
			'model' => $model,
		]);
		
	}
	
	/**
	 * Displays a single Company model.
	 *
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionView($id) {
		
		// Load model
		$model = $this->loadModel($id);
		
		// Set visit
		$model->setStat();
		
		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}
	
	/**
	 * List all news
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionNews($id) {
		return $this->_content($id,News::class, 'news');
	}
	
	/**
	 * List all articles
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionArticles($id) {
		return $this->_content($id,Article::class, 'articles');
	}
	
	/**
	 * List all blogs
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionBlogs($id) {
		return $this->_content($id,Blog::class, 'blogs');
	}
	
	/**
	 * List all portfolio
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	public function actionPortfolio($id) {
		return $this->_content($id,Portfolio::class, 'portfolio');
	}
	
	/**
	 * Lists all subscribers.
	 *
	 * @param int $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\db\Exception
	 */
	public function actionSubscribers($id) {
		
		// Get company
		$model = $this->loadModel($id);
		
		$query = User::find()->joinWith([
			'profile',
			'telegram',
			'address'
		])->subscribers(Vote::COMPANY_FAVORITE, $id)->votes();
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('subscribers', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * @param $id
	 * @param $class
	 * @param $type
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	private function _content($id, $class, $type) {
		
		// Load model
		$model = $this->loadModel($id);
		
		// Set visit
		$model->setStat();
		
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = $class::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'tags',
			'company',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			$class::tableName().'.company_id' => $id,
			$class::tableName().'.status' => Status::ENABLED
		])->votes()->limit(10);
		
		$query->orderBy([
			'pinned' => SORT_DESC,
			'date_at' => SORT_DESC,
		]);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render($type, [
			'dataProvider' => $dataProvider,
			'model' => $model,
		]);
	}
	
	/**
	 * Load company model
	 * @param $id
	 *
	 * @return Company
	 * @throws NotFoundHttpException
	 * @throws \yii\db\Exception
	 */
	private function loadModel($id) {
		
		/** @var Company $model */
		$query = Company::find()->joinWith([
			'contentsStat',
		])->andWhere(Company::tableName().'.id = :id', [
			':id' => $id,
		])->votes();
		
		$model = $query->one();
		if (!$model)
			throw new NotFoundHttpException(Yii::t('company', 'error_not_exists'));
		
		if ($model->status != Status::ENABLED && (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)) {
			$usersIds = $model->getUsers()->select('id')->createCommand()->queryColumn();
			if (!in_array(Yii::$app->user->id, $usersIds))
				throw new NotFoundHttpException(Yii::t('company', 'error_not_exists'));
		}
		
		return $model;
	}
}