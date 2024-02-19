<?php
namespace client\controllers;

use client\components\Controller;

use common\modules\base\components\Debug;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

use common\modules\base\helpers\enum\Status;

use common\modules\vote\models\Vote;

use common\modules\content\models\Article;
use common\modules\content\models\News;
use common\modules\content\models\Blog;

use common\modules\project\models\Project;

use common\modules\plugin\models\Plugin;

use common\modules\tag\models\Tag;

use common\modules\user\models\User;

use common\modules\company\models\Company;

class FavoritesController extends Controller
{
	
	/**
	 * Lists all Article models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex() {
		
		$query = Article::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->votes()->voted(Vote::CONTENT_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all News models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionNews() {
		$query = News::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->votes()->voted(Vote::CONTENT_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_news', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all Blog models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionBlog() {
		$query = Blog::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->votes()->voted(Vote::CONTENT_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_blog', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all Project models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionProject() {
		$query = Project::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->votes()->voted(Vote::CONTENT_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_project', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all Plugins models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionPlugin() {
		$query = Plugin::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->votes()->voted(Vote::CONTENT_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_plugin', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all User models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionAuthor() {
		$query = User::find()->joinWith([
			'profile',
			'telegram',
			'address',
			'contentsStat',
			'tags',
			'mediaAvatar',
		])->votes()->voted(Vote::USER_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_author', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all Company models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionCompany() {
		$query = Company::find()->joinWith([
			'contentsStat',
			'media',
		])->votes()->voted(Vote::COMPANY_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_company', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * Lists all Tag models.
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionTag() {
		$query = Tag::find()->joinWith([
		])->votes()->voted(Vote::TAG_FAVORITE);
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index_tag', [
			'dataProvider' => $dataProvider,
			'items' => $this->_getItems(),
		]);
	}
	
	/**
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 */
	private function _getItems() {
		return [
			[
				'label' => Yii::t('favorite', 'tab_article'),
				'url' => ['favorites/index'],
				'visible' => Yii::$app->user->can('favorites.index'),
			],
			[
				'label' => Yii::t('favorite', 'tab_news'),
				'url' => ['favorites/news'],
				'visible' => Yii::$app->user->can('favorites.news') && News::find()->votes()->voted(Vote::CONTENT_FAVORITE)->count(),
			],
			[
				'label' => Yii::t('favorite', 'tab_blog'),
				'url' => ['favorites/blog'],
				'visible' => Yii::$app->user->can('favorites.blog') && Blog::find()->votes()->voted(Vote::CONTENT_FAVORITE)->count(),
			],
			[
				'label' => Yii::t('favorite', 'tab_project'),
				'url' => ['favorites/project'],
				'visible' => Yii::$app->user->can('favorites.project') && Project::find()->votes()->voted(Vote::CONTENT_FAVORITE)->count(),
			],
			[
				'label' => Yii::t('favorite', 'tab_plugin'),
				'url' => ['favorites/plugin'],
				'visible' => Yii::$app->user->can('favorites.plugin') && Plugin::find()->votes()->voted(Vote::CONTENT_FAVORITE)->count(),
			],
			[
				'label' => Yii::t('favorite', 'tab_author'),
				'url' => ['favorites/author'],
				'visible' => Yii::$app->user->can('favorites.author') && User::find()->votes()->voted(Vote::USER_FAVORITE)->count(),
			],
			[
				'label' => Yii::t('favorite', 'tab_company'),
				'url' => ['favorites/company'],
				'visible' => Yii::$app->user->can('favorites.company') && Company::find()->votes()->voted(Vote::COMPANY_FAVORITE)->count(),
			],
			[
				'label' => Yii::t('favorite', 'tab_tag'),
				'url' => ['favorites/tag'],
				'visible' => Yii::$app->user->can('favorites.tag') && Tag::find()->votes()->voted(Vote::TAG_FAVORITE)->count(),
			]
		];
	}
}