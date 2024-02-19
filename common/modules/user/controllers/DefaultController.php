<?php
namespace common\modules\user\controllers;

use Yii;
use yii\web\Response;
use yii\db\Query;

use common\modules\base\components\Controller;

use common\modules\media\helpers\enum\Mode;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

class DefaultController extends Controller
{
	/**
	 * Search users
	 * @param string $q
	 *
	 * @return array
	 */
	public function actionSearch($q = null, $page = 1, $limit = 100) {
		
		// Set output format
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		// Prepare result
		$result = [
			'count' => 0,
			'items' => [],
		];
		
		if (!is_null($q) && strlen($q) >= 3) {
			
			// Create query
			$query = new Query;
			$query->select('u.id, u.email, up.phone, u.username, up.first_name, up.last_name, up.middle_name');
			$query->from(User::tableName().' u');
			$query->leftJoin(UserProfile::tableName().' up', 'up.user_id = u.id');
			$query->where(['like', 'u.username', $q]);
			$query->orWhere(['like', 'up.first_name', $q]);
			$query->orWhere(['like', 'up.last_name', $q]);
			$query->orWhere(['like', 'up.middle_name', $q]);
			$query->limit($limit);
			$query->offset(($page - 1) * $limit);
			
			// Get count
			$result['count'] = $query->count();
			
			// Get rows
			$rows = $query->all();
			if ($rows) {
				foreach ($rows as $row) {
					$tmp = [];
					if (strlen($row['last_name']))
						$tmp[] = $row['last_name'];
					if (strlen($row['first_name']))
						$tmp[] = $row['first_name'];
					if (strlen($row['middle_name']))
						$tmp[] = $row['middle_name'];
					$row['fio'] = implode(' ', $tmp);
					$result['items'][] = $row;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Search users
	 *
	 * @param string $q
	 *
	 * @return array
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionSearchJson($q) {
		$q = urldecode($q);
		
		$result = [];
		
		$query = User::find()->joinWith([
			'profile',
			'telegram',
		])->limit(20);
		
		$query->andFilterWhere(['like', User::tableName().'.username', $q]);
		$query->orFilterWhere(['like', User::tableName().'.email', $q]);
		$query->orFilterWhere(['like', UserProfile::tableName().'.phone', $q]);
		$query->orFilterWhere(['like', 'telegram.username', $q]);
		$query->orFilterWhere(['like', 'CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.UserProfile::tableName().'.middle_name)', $q]);
		
		$models = $query->all();
		if ($models) {
			foreach ($models as $model) {
				$result[] = $model->getInfo();
			}
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		return $result;
	}
}