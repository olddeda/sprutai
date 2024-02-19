<?php
namespace client\controllers;

use client\components\Controller;

use common\modules\base\components\Debug;
use common\modules\base\components\jira\User;
use common\modules\tag\models\Tag;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Status as ContentStatus;
use common\modules\content\helpers\enum\Type as ContentType;


/**
 * Video controller
 */
class VideoController extends Controller
{
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
					],
				],
			],
		]);
	}
	
	
	public function actionIndex() {
		
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
			Content::tableName().'.status' => ContentStatus::ENABLED,
			Tag::tableName().'.id' => 134,
		])->andWhere([
			'in',
			Content::tableName().'.type',
			[
				ContentType::ARTICLE,
				ContentType::BLOG,
				ContentType::NEWS,
			],
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
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}
