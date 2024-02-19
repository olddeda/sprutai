<?php
namespace client\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

use client\components\Controller;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\rbac\components\AccessControl;

use common\modules\user\models\User;

use common\modules\content\models\Content;
use common\modules\content\models\ContentTag;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\helpers\enum\Status as ContentStatus;

use common\modules\menu\models\Menu;

use common\modules\tag\models\Tag;

class MenusController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['view'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Displays a single Menu model.
	 * @param integer $id
	 *
	 * @return string
	 */
	public function actionView($id) {
		$conditions = [];
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)
			$conditions['status'] = Status::ENABLED;
		
		// Find model
		$model = Menu::findById($id, true, 'menu', [], false, $conditions);
		
		if ($tag = Yii::$app->request->get('tag')) {
			return $this->actionContent($model, $tag);
		}
		
		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}
	
	public function actionTag($id, $tag) {
	
	}
	
	private function actionContent($model, $tag) {
		$tag = Yii::$app->request->get('tag');
		
		$tagModel = Tag::findByColumn('title', $tag, true, 'tag');
		
		$tagIds = [$tagModel->id];
		
		$subtagModel = null;
		if ($subtag = Yii::$app->request->get('subtag')) {
			$subtagModel = Tag::findByColumn('title', $subtag, true, 'tag');
			$tagIds[] = $subtagModel->id;
		}
		
		$contentIds = (new Query())
			->select('content_id')
			->from(['a' => ContentTag::tableName()])
			->where(['in', 'tag_id', $tagIds])
			->andWhere('EXISTS (
				SELECT 1
				FROM '.ContentTag::tableName().' b
				WHERE a.content_id = b.content_id
				GROUP BY content_id
				HAVING COUNT(DISTINCT tag_id) >= :count
			)', [
				':count' => count($tagIds),
			])
			->groupBy('a.content_id')
			->having('COUNT(*) >= :count', [
				':count' => count($tagIds),
			])
			->column();
		
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = Content::find()->joinWith([
			'media',
			'statistics',
			'stat',
			'tags',
			'tagModule',
			'company',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->where([
			Content::tableName().'.status' => ContentStatus::ENABLED,
		])->andWhere([
			'in',
			Content::tableName().'.type',
			[
				ContentType::NEWS,
				ContentType::ARTICLE,
				ContentType::BLOG,
				ContentType::PLUGIN,
				ContentType::PROJECT,
			],
		])->andWhere([
			'in',
			Content::tableName().'.id',
			$contentIds,
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
		return $this->render('content', [
			'menuModel' => $model,
			'tagModel' => $tagModel,
			'subtagModel' => $subtagModel,
			'dataProvider' => $dataProvider,
		]);
	}
}