<?php
namespace api\models\telegram\search;

use api\models\tag\Tag;
use api\models\telegram\TelegramChat;
use common\modules\base\helpers\enum\Status;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * TelegramChatSearch represents the model behind the search form about `app\models\telegram\TelegramChat`.
 */
class TelegramChatSearch extends TelegramChat
{
    /**
     * @var bool
     */
    public $isAdmin = false;

    /**
     * @var string
     */
    public $tags_title;

    /**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'is_channel', 'is_partner', 'notify_content', 'notify_payment', 'members_count', 'identifier', 'status'], 'integer'],
            [['title', 'username'], 'string'],
            [['created_at', 'updated_at', 'tags_title'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
     */
	public function search($params = []) {
        $query = TelegramChat::find()
            ->alias('t')
            ->with([
                'media',
            ])
            ->joinWith([
                'tags'
            ], true)
            ->where([
                'not in', 't.status', [Status::TEMP, Status::DELETED]
            ])
            ->groupBy('t.id')
        ;

		// Create data provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
                'pageSizeLimit' => [1, 150],
			],
            'sort' => [
                'params' => $params,
                'attributes' => [
                    'id',
                    'title',
                    'username',
                    'identifier',
                    'is_channel',
                    'is_partner',
                    'notify_content',
                    'notify_payment',
                    'members_count',
                    'status',
                    'created_at',
                    'updated_at',
                    'tags_title' => [
                        'asc' => [Tag::tableName().'.title' => SORT_ASC],
                        'desc' => [Tag::tableName().'.title' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
		]);

		$this->load(ArrayHelper::getValue($params, 'filter', []), '');

		if (!$this->validate()) {
			return $dataProvider;
		}

		if (!$this->isAdmin && !$this->status && !Yii::$app->user->getIsAdmin())
		    $this->status = Status::ENABLED;

		$query->andFilterWhere([
		    't.is_channel' => $this->is_channel,
		    't.is_partner' => $this->is_partner,
		    't.notify_content' => $this->notify_content,
            't.notify_payment' => $this->notify_payment,
            't.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 't.id', $this->id]);
        $query->andFilterWhere(['like', 't.title', $this->title]);
        $query->andFilterWhere(['like', 't.username', $this->username]);
        $query->andFilterWhere(['like', 't.identifier', $this->identifier]);
        $query->andFilterWhere(['like', 't.members_count', $this->members_count]);
        $query->andFilterWhere(['like', Tag::tableName().'.title', $this->tags_title]);

        if (is_array($this->created_at) && (isset($this->created_at['start']) || isset($this->created_at['end'])) && $this->created_at['start'] && $this->created_at['end']) {
            $start = $this->created_at['start'];
            $end = $this->created_at['end'];
            $query->andFilterWhere([
                'between',
                'FROM_UNIXTIME(t.created_at, "%d-%m-%Y")',
                $start,
                $end,
            ]);
        }
        else if ($this->created_at && is_scalar($this->created_at)) {
            $query->andFilterWhere([
                'FROM_UNIXTIME(t.created_at, "%d-%m-%Y")' => $this->created_at,
            ]);
        }

		return $dataProvider;
	}
}