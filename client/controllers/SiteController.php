<?php
namespace client\controllers;

use client\components\Controller;
use common\modules\content\helpers\enum\Status as ContentStatus;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\models\Content;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;


/**
 * Site controller
 */
class SiteController extends Controller
{
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => ['index', 'error', 'telegram', 'test'],
						'allow' => true,
					],
				],
			],
		]);
	}
	
	/**
     * @inheritdoc
     */
    public function actions()  {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
	
	public function actionIndex() {
  
		/** @var \common\modules\content\models\query\ContentQuery $query */
		$query = Content::find()->joinWith([
			'statistics',
			'stat',
			'tags',
			'company',
			'author' => function($query) {
				$query->joinWith('profile');
			},
		])->with([
            'media',
            'mediaLogo'
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
				ContentType::PORTFOLIO,
				ContentType::EVENT,
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
	
	public function actionTelegram($username) {
    	$url = 'tg://resolve?domain='.$username;
    	return $this->render('telegram', [
    		'username' => $username,
    		'url' => $url,
		]);
	}
	
	public function actionTest() {
    	
    	return $this->render('test', [
    	
		]);
	}
}
