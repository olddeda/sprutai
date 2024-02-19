<?php
namespace common\modules\comments\models\search;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\comments\helpers\enum\Status;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;

use common\modules\comments\models\Comment;

/**
 * Class CommentSearch
 * @package common\modules\comments\models\search
 */
class CommentSearch extends Comment
{
	public $author_search;

    /**
     * Returns the validation rules for attributes.
     * @return array validation rules
     */
    public function rules() {
        return ArrayHelper::merge(parent::rules(), [
            [['id', 'module_type', 'content', 'status', 'related_to', 'author_search', 'created_at'], 'safe'],
        ]);
    }

    /**
     * Setup search function for filtering and sorting based on fullName field
     * @param $params
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
        $query = self::find()->where(['!= ', 'status', Status::TEMP]);

		$query->joinWith([
			'author' => function ($query) {
				$query->joinWith(['profile']);
			},
		]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);

        $dataProvider->setSort([
			'attributes' => [
				'id',
				'module_type',
				'content',
				'related_to',
				'status',
				'created_at',
				'author_search' => [
					'asc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.User::tableName().'.username)' => SORT_ASC],
					'desc' => ['CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.User::tableName().'.username)' => SORT_DESC],
				],
			],
            'defaultOrder' => [
				'id' => SORT_DESC
			],
        ]);

        // load the search form data and validate
        if (!($this->load($params))) {
            return $dataProvider;
        }

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'module_type' => $this->module_type,
			'status' => $this->status,
		]);

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', self::tableName().'.content', $this->content]);
		$query->andFilterWhere(['like', self::tableName().'.related_to', $this->related_to]);
		$query->andFilterWhere(['like', 'CONCAT('.UserProfile::tableName().'.last_name, " ", '.UserProfile::tableName().'.first_name, " ", '.User::tableName().'.username)', $this->author_search]);

		// Add filter time condition
		$query->andFilterWhere([
			'FROM_UNIXTIME('.self::tableName().'.created_at, "%d-%m-%Y")' => $this->created_at,
		]);

        return $dataProvider;
    }
}