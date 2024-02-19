<?php
namespace client\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\sphinx\Query;
use yii\data\ActiveDataProvider;
use yii\web\Response;

use common\modules\base\components\Debug;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Status as ContentStatus;
use common\modules\content\helpers\enum\Type as ContentType;

use client\components\Controller;

/**
 * Class SearchController
 * @package client\controllers
 */
class SearchController extends Controller
{
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => ['index', 'json'],
						'allow' => true,
					],
				],
			],
		]);
	}
	
	public function actionIndex($query) {
		
		/** @var array $ids */
		$ids = (new Query())->select('id')->from('idx_content')->match($query)->groupBy('id')->column();
		
		/** @var \yii\db\Query $query */
		$queryArticle = Content::find()->where([
			'status' => ContentStatus::ENABLED
		])->andWhere([
			'in', Content::tableName().'.id', $ids
		])->andWhere([
			'in',
			Content::tableName().'.type',
			[ContentType::NEWS, ContentType::ARTICLE, ContentType::BLOG],
		])->orderBy('date_at DESC')->votes();
		
		$dataProvider = new ActiveDataProvider([
			'query' => $queryArticle,
			'pagination' => [
				'pageSize' => 10,
			],
		]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'query' => Html::encode($query),
		]);
	}
	
	public function actionJson($query) {
		
		/** @var array $ids */
		$ids = (new Query())->select('id')->from('idx_content')->match($query)->groupBy('id')->column();
		
		/** @var \yii\db\Query $query */
		$queryArticle = Content::find()->select([
			'id', 'title',
		])->where([
			'status' => ContentStatus::ENABLED,
		])->andWhere([
			'in', Content::tableName().'.id', $ids
		])->andWhere([
			'in',
			Content::tableName().'.type',
			[ContentType::NEWS, ContentType::ARTICLE, ContentType::BLOG],
		])->orderBy('date_at DESC')->limit(20)->asArray()->all();
		
		return $this->asJson($queryArticle);
	}
}
