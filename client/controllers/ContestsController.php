<?php
namespace client\controllers;

use common\modules\tag\models\Tag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use client\components\Controller;

use common\modules\content\helpers\enum\Status;
use common\modules\content\helpers\enum\Type;
use common\modules\content\models\Content;

use common\modules\vote\models\Vote;
use yii\web\NotFoundHttpException;

/**w
 * Class ContestsController
 * @package client\controllers
 */
class ContestsController extends Controller
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
						'actions' => ['view'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	public function actionView(string $date) {
		//if ($date != '03-2019')
		//	throw new NotFoundHttpException();

        $tags = Yii::$app->request->get('tags', null);
        if ($tags) {
            $tags = explode(',', $tags);
        }
		
		$query = Content::find()->joinWith([
			'statistics',
			'stat',
			'tags',
			'company',
			'media',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->where([
			Content::tableName().'.status' => Status::ENABLED,
		])->andWhere([
			'in',
			Content::tableName().'.type',
			[Type::ARTICLE, Type::BLOG],
		])->andWhere([
			'FROM_UNIXTIME('.Content::tableName().'.date_at, "%m-%Y")' => $date,
		])->orderBy(['contestVotePositive' => SORT_DESC]);
		
		$query->withVoteAggregate(Vote::CONTEST_VOTE);
		$query->withUserVote(Vote::CONTEST_VOTE);

		if ($tags) {
		    $query->andWhere([
		        'in', Tag::tableName().'.title', $tags,
            ]);
        }
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 0,
			],
		]);
		
		$canVote = false;
		if (!Yii::$app->user->isGuest) {
			$canVote = Content::find()->where([
				'status' => Status::ENABLED,
				'author_id' => Yii::$app->user->id,
			]);
		}
		
		// Render view
		return $this->render('view', [
			'dataProvider' => $dataProvider,
			'date' => $date,
			'canVote' => $canVote,
			'showCounters' => (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor())
		]);
	
	}
}