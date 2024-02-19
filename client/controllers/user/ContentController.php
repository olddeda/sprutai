<?php
namespace client\controllers\user;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use common\modules\base\components\Controller;

use common\modules\content\helpers\enum\Status;

use common\modules\user\models\User;

use common\modules\content\models\Article;
use common\modules\content\models\News;
use common\modules\content\models\Blog;
use common\modules\project\models\Project;
use common\modules\plugin\models\Plugin;

use common\modules\vote\models\Vote;

class ContentController extends Controller
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
						'actions' => ['article', 'news', 'blog', 'project', 'plugin'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all Articles models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionArticle(int $id = 0) {
		if (!$id)
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		$query = Article::find()->joinWith([
			'media',
			'tags',
			'paymentTypes',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			Article::tableName().'.status' => Status::ENABLED,
			Article::tableName().'.author_id' => $model->id,
		])->votes();
		
		$query->orderBy([Article::tableName().'.created_at' => SORT_DESC]);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('article', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Lists all News models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionNews(int $id = 0) {
		if (!$id)
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		$query = News::find()->joinWith([
			'media',
			'tags',
			'paymentTypes',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			News::tableName().'.status' => Status::ENABLED,
			News::tableName().'.author_id' => $model->id,
		])->votes();
		
		$query->orderBy([News::tableName().'.created_at' => SORT_DESC]);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('news', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Lists all Blog models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionBlog(int $id = 0) {
		if (!$id)
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		$query = Blog::find()->joinWith([
			'media',
			'tags',
			'paymentTypes',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			Blog::tableName().'.status' => Status::ENABLED,
			Blog::tableName().'.author_id' => $model->id,
		])->votes();
		
		$query->orderBy([Blog::tableName().'.created_at' => SORT_DESC]);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('blog', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Lists all Project models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionProject(int $id = 0) {
		if (!$id)
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		$query = Project::find()->joinWith([
			'media',
			'tags',
			'paymentTypes',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			Project::tableName().'.status' => Status::ENABLED,
			Project::tableName().'.author_id' => $model->id,
		])->votes();
		
		$query->orderBy([Project::tableName().'.created_at' => SORT_DESC]);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('project', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Lists all Plugin models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionPlugin(int $id = 0) {
		if (!$id)
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		$query = Plugin::find()->joinWith([
			'media',
			'tags',
			'paymentTypes',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->andWhere([
			Plugin::tableName().'.status' => Status::ENABLED,
			Plugin::tableName().'.author_id' => $model->id,
		])->votes();
		
		$query->orderBy([Plugin::tableName().'.created_at' => SORT_DESC]);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('plugin', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
}