<?php
namespace common\modules\content\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\base\helpers\enum\Status;

use common\modules\content\models\Article;

/**
 * ContestController implements the CRUD actions for Page model.
 */
class ContestController extends Controller
{
	
	/**
	 * Lists all Content models.
	 *
	 * @return string
	 * @throws \yii\db\Exception
	 */
	public function actionIndex() {
		
		$db = Yii::$app->db;
		
		$min = '2018-11-01 00:00:00';
		$max = $db->createCommand("
			SELECT MAX(DATE_FORMAT(FROM_UNIXTIME(date_at), '%Y-%m'))
			FROM ".Article::tableName()."
		")->queryScalar();
		$max.= '-31 29:59:59';
		
		$dates = $db->createCommand("
			SELECT DATE_FORMAT(FROM_UNIXTIME(date_at), '%Y-%m')
			FROM ".Article::tableName()."
			WHERE DATE_FORMAT(FROM_UNIXTIME(date_at), '%Y-%m-%d %H:%m:%s') BETWEEN :min AND :max
			GROUP BY DATE_FORMAT(FROM_UNIXTIME(date_at), '%Y-%m')
		", [
			':min' => $min,
			':max' => $max,
		])->queryColumn();
		
		$ids = [];
		foreach ($dates as $date) {
			$query = Article::find()->andWhere([
				Article::tableName().'.status' => Status::ENABLED,
			])->andWhere(["DATE_FORMAT(FROM_UNIXTIME(date_at), '%Y-%m')" => $date])->votes()->select([
					Article::tableName().'.id',
					'MAX(`articleVoteAggregate`.`positive`) AS vote',
					'DATE_FORMAT(FROM_UNIXTIME(date_at), "%Y-%m") AS date']
			)->orderBy('vote DESC');
			$result = $query->scalar();
			if ($result)
				$ids[] = $result;
		}
		
		$query = Article::find()->andWhere([
			Article::tableName().'.status' => Status::ENABLED,
		])->andWhere(['in', Article::tableName().'.id', $ids])->votes()->orderBy('date_at DESC');
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}