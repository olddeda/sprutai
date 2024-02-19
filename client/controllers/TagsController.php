<?php
namespace client\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;

use common\modules\tag\models\Tag;

use common\modules\content\models\Content;
use common\modules\content\models\ContentAuthorStat;
use common\modules\content\models\ContentCompanyStat;
use common\modules\content\helpers\enum\Type;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

use common\modules\company\models\Company;

use client\components\Controller;

class TagsController extends Controller
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
						'actions' => ['index', 'view', 'news', 'blogs', 'projects', 'plugins', 'authors', 'companies'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all Tag models.
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex() {
		
		// Prepare query
		$query = Tag::find()->joinWith([
		])->andWhere([
			Tag::tableName().'.status' => Status::ENABLED,
		])->limit(20)->orderBy([
			Tag::tableName().'.title' => SORT_ASC,
		])->votes();
		
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
		]);
	}
	
	/**
	 * Show articles by tag
	 *
	 * @param string $title
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionView($title, $type = 'article') {
		$conditions = [];
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)
			$conditions['status'] = Status::ENABLED;
		
		/** @var \common\modules\tag\models\query\TagQuery $model */
		$query = Tag::find()->where(Tag::tableName().'.title = :title', [
			':title' => $title,
		])->votes();
		
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
			$query->andWhere([
				Tag::tableName().'.status' => Status::ENABLED,
			]);
		}
		
		/** @var Tag $model */
		$model = $query->one();
		if (is_null($model))
			throw new NotFoundHttpException(Yii::t('tag', 'error_not_exists'));
		
		// Set visit
		$model->setStat();
		
		$class = 'common\modules\content\models\Article';
		$typeId = Type::ARTICLE;
		if ($type == 'news') {
			$typeId = Type::NEWS;
			$class = 'common\modules\content\models\News';
		}
		else if ($type == 'blog') {
			$typeId = Type::BLOG;
			$class = 'common\modules\content\models\Blog';
		}
		else if ($type == 'projects') {
			$typeId = Type::PROJECT;
			$class = 'common\modules\project\models\Project';
		}
		else if ($type == 'plugins') {
			$typeId = Type::PLUGIN;
			$class = 'common\modules\plugin\models\Plugin';
		}
		
		// Prepare content query
		$query = $class::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'tags',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->where([
			Content::tableName().'.status' => Status::ENABLED,
			Content::tableName().'.type' => $typeId,
		])->andWhere([
			'in', Tag::tableName().'.id', $model->id,
		])->votes()->orderBy(['date_at' => SORT_DESC])->limit(10);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'type' => $type,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Show news by tag
	 *
	 * @param $title
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionNews($title) {
		return $this->actionView($title, 'news');
	}
	
	/**
	 * @param $title
	 *w
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionBlogs($title) {
		return $this->actionView($title, 'blog');
	}
	
	/**
	 * Show projects by tag
	 *
	 * @param $title
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionProjects($title) {
		return $this->actionView($title, 'projects');
	}
	
	/**
	 * Show plugins by tag
	 *
	 * @param $title
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionPlugins($title) {
		return $this->actionView($title, 'plugins');
	}
	
	/**
	 * Show authors by tag
	 *
	 * @param $title
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionAuthors($title) {
		
		$conditions = [];
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)
			$conditions['status'] = Status::ENABLED;
		
		/** @var \common\modules\tag\models\query\TagQuery $model */
		$query = Tag::find()->where(Tag::tableName().'.title = :title', [
			':title' => $title,
		])->votes();
		
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
			$query->andWhere([
				Tag::tableName().'.status' => Status::ENABLED,
			]);
		}
		
		/** @var Tag $model */
		$model = $query->one();
		
		if (is_null($model))
			throw new NotFoundHttpException();
		
		// Set visit
		$model->setStat();
		
		// Prepare user query
		$query = User::find()
			->joinWith([
				'profile',
				'address',
				'telegram',
				'mediaAvatar',
				'tags',
				'contentsStat'
			])
			->andWhere('deleted_at IS NULL')
			->andWhere('
				'.ContentAuthorStat::tableName().'.articles > 0 OR
				'.ContentAuthorStat::tableName().'.news > 0 OR
				'.ContentAuthorStat::tableName().'.blogs > 0 OR
				'.ContentAuthorStat::tableName().'.projects > 0 OR
				'.ContentAuthorStat::tableName().'.plugins > 0
			')
			->andWhere([
				'in', Tag::tableName().'.id', $model->id,
			])
			->votes()
			->orderBy([
				UserProfile::tableName().'.last_name' => SORT_ASC,
				UserProfile::tableName().'.first_name' => SORT_ASC,
			]);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'type' => 'author',
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Show companies by tag
	 *
	 * @param $title
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionCompanies($title) {
		
		$conditions = [];
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)
			$conditions['status'] = Status::ENABLED;
		
		/** @var \common\modules\tag\models\query\TagQuery $model */
		$query = Tag::find()->where(Tag::tableName().'.title = :title', [
			':title' => $title,
		])->votes();
		
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor) {
			$query->andWhere([
				Tag::tableName().'.status' => Status::ENABLED,
			]);
		}
		
		/** @var Tag $model */
		$model = $query->one();
		
		// Set visit
		$model->setStat();
		
		// Prepare user query
		$query = Company::find()
			->joinWith([
				'media',
				'tags',
				'contentsStat'
			])
			->andWhere([Company::tableName().'.status' => Status::ENABLED])
			->andWhere('
				'.ContentCompanyStat::tableName().'.articles > 0 OR
				'.ContentCompanyStat::tableName().'.news > 0 OR
				'.ContentCompanyStat::tableName().'.blogs > 0 OR
				'.ContentCompanyStat::tableName().'.projects > 0 OR
				'.ContentCompanyStat::tableName().'.plugins > 0
			')
			->andWhere([
				'in', Tag::tableName().'.id', $model->id,
			])
			->votes()
			->orderBy([
				Company::tableName().'.title' => SORT_ASC,
			]);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'type' => 'companies',
			'dataProvider' => $dataProvider,
		]);
	}
}