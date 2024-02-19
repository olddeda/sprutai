<?php
namespace client\controllers;

use common\modules\content\models\ContentAuthorStat;
use common\modules\content\models\ContentTag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Debug;

use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;

use common\modules\rbac\components\AccessControl;

use common\modules\media\models\Media;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;
use common\modules\user\models\UserAddress;
use common\modules\user\models\UserAccount;

use common\modules\content\models\Content;

use client\components\Controller;

class AuthorController extends Controller
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
						'actions' => ['index'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Lists all Author models.
	 *
	 * @return string
	 * @throws \Throwable
	 * @throws \yii\base\InvalidConfigException
	 */
	public function actionIndex() {
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
			->votes()
			->orderBy('userFavoritePositive DESC');
		
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