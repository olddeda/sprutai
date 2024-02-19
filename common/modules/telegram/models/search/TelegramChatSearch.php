<?php
namespace common\modules\telegram\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\modules\tag\models\Tag;

use common\modules\telegram\models\TelegramChat;

/**
 * TelegramChatSearch represents the model behind the search form of `common\modules\telegram\models\TelegramChat`.
 */
class TelegramChatSearch extends TelegramChat
{
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'identifier', 'username'], 'string'],
			[['notify_content', 'notify_payment', 'is_partner'], 'boolean'],
			[['tags_ids'], 'safe'],
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
    public function search($params) {
        $query = TelegramChat::find()
			->joinWith(['tags']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
	
		// Set data provider sort
		$dataProvider->setSort([
			'attributes' => [
				'id',
				'title',
				'identifier',
				'username',
				'notify_content',
				'notify_payment',
				'is_partner',
				'status',
				'created_at',
				'updated_at',
				'tags_ids' => [
					'asc' => [Tag::tableName().'.title' => SORT_ASC],
					'desc' => [Tag::tableName().'.title' => SORT_DESC],
				],
			],
			'defaultOrder' => [
				'created_at' => SORT_ASC,
			],
		]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			self::tableName().'.id' => $this->id,
			self::tableName().'.notify_content' => $this->notify_content,
			self::tableName().'.notify_payment' => $this->notify_payment,
            self::tableName().'.is_partner' => $this->is_partner,
			self::tableName().'.status' => $this->status,
			self::tableName().'.created_at' => $this->created_at,
			self::tableName().'.updated_at' => $this->updated_at,
        ]);
	
		$query->andFilterWhere([Tag::tableName().'.id' => $this->tags_ids]);
        
        $query->andFilterWhere(['like', self::tableName().'.title', $this->title]);
		$query->andFilterWhere(['like', self::tableName().'.identifier', $this->identifier]);
		$query->andFilterWhere(['like', self::tableName().'.username', $this->username]);

        return $dataProvider;
    }
}
