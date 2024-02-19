<?php
namespace api\modules\v1\controllers\achievement;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use common\modules\achievement\helpers\enum\Type;
use common\modules\achievement\models\AchievementUserStat;

use api\modules\v1\components\ActiveController as Controller;
use api\modules\v1\components\actions\IndexAction;
use api\models\achievement\Achievement;
use api\models\achievement\AchievementUser;

use api\models\achievement\search\AchievementUserSearch;

/**
 * Class UserController
 * @package api\modules\v1\controllers/achievement
 */
class UserController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\achievement\AchievementUser';

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['index', 'top'],
            ],
            'access' => [
                'except' => ['index', 'top'],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['dataFilter'] = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => 'api\models\achievement\search\AchievementUserSearch',
        ];

        return $actions;
    }

    /**
     * @param IndexAction $action
     * @param $filter
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(IndexAction $action, $filter) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var AchievementUserSearch $searchModel */
        $searchModel = new AchievementUserSearch();
        return $searchModel->search($requestParams);
    }

    /**
     * @OA\Get(
     *     path="/achievements/users",
     *     tags={"achievement user"},
     *     summary="Получение списка достижений пользователя",
     *     description="Возвращает достижений пользователя",
     *     operationId="achievement_user_list",
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/AchievementUser")
     *             ),
     *             @OA\Property(
     *                 property="_links",
     *                 @OA\Items(ref="#/components/schemas/Links")
     *             ),
     *             @OA\Property(
     *                 property="_meta",
     *                 @OA\Items(ref="#/components/schemas/Meta")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/AchievementUser")
     *             ),
     *             @OA\Property(
     *                 property="_links",
     *                 @OA\Items(ref="#/components/schemas/Links")
     *             ),
     *             @OA\Property(
     *                 property="_meta",
     *                 @OA\Items(ref="#/components/schemas/Meta")
     *             )
     *         )
     *     )
     * )
     */

    public function actionTop() {
        $tmp = [];
        foreach (Type::$list as $type => $name) {
            $query = (new Query())
                ->select([
                    'a.id',
                    'au.user_id',
                ])
                ->from(['au' => AchievementUser::tableName()])
                ->leftJoin(['a' => Achievement::tableName()], 'a.id = au.achievement_id')
                ->leftJoin(['aus' => AchievementUserStat::tableName()], 'aus.user_id = au.user_id AND aus.type = a.type')
                ->where('au.achievement_id IS NOT NULL AND a.type = :type', [
                    ':type' => $type
                ])
                ->orderBy([
                    'aus.count' => SORT_DESC,
                    'a.level' => SORT_DESC,
                ])
                ->limit(1)
            ;

            $item = $query->one();
            if ($item) {
                $achievementUser = AchievementUser::find()
                    ->select('*')
                    ->addSelect([
                        '(
                            SELECT c.count 
                            FROM '.AchievementUserStat::tableName().' AS c
                            WHERE c.type = '.Achievement::tableName().'.type
                            AND c.user_id = '.AchievementUser::tableName().'.user_id
                        ) AS count'
                    ])
                    ->joinWith([
                        'achievement',
                    ])
                    ->with([
                        'user' => function ($query) {
                            $query->with('profile');
                        }
                    ])
                    ->where([
                        'achievement_id' => $item['id'],
                        'user_id' => $item['user_id'],
                    ])->one()
                ;
                if ($achievementUser) {
                    $tmp[] = $achievementUser;
                }
             }
        }
        return [
            'items' => $tmp,
        ];
    }
}
