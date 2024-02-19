<?php

namespace common\modules\rbac\models\search;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class Search extends Model
{
    /**
	 * @var string
	 */
    public $name;
    
    /**
	 * @var string
	 */
    public $description;
    
    /**
	 * @var string
	 */
    public $rule_name;
    
    /**
	 * @var \common\modules\rbac\components\DbManager
	 */
    protected $manager;
    
    /**
	 * @var int
	 */
    protected $type;

    /**
	 * @inheritdoc
	 */
    public function __construct($type, $config = []) {
        parent::__construct($config);
        $this->manager = Yii::$app->authManager;
        $this->type = $type;
    }

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name', 'description', 'rule_name'], 'string'],
		];
	}
    
    /**
	 * @inheritdoc
	 */
    public function scenarios() {
        return [
            'default' => ['name', 'description', 'rule_name'],
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'name' => Yii::t('rbac', 'field_name'),
			'description' => Yii::t('rbac', 'field_description'),
			'children' => Yii::t('rbac', 'field_children'),
			'rule' => Yii::t('rbac', 'field_rule'),
		];
	}
    
    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params = []) {

		// Create query
        $query = (new Query)->select([
			'name',
			'description',
			'rule_name'
		])->andWhere([
			'type' => $this->type
		])->from($this->manager->itemTable);

		// Load and validate
        if ($this->load($params) && $this->validate()) {
            $query->andFilterWhere(['like', 'name', $this->name]);
			$query->andFilterWhere(['like', 'description', $this->description]);
			$query->andFilterWhere(['like', 'rule_name', $this->rule_name]);
        }

		// Create data provider
		$dataProvider = new ArrayDataProvider([
			'allModels' => $query->all($this->manager->db),
			'sort' => [
				'attributes' => [
					'name' => [
						'header' => Yii::t('rbac', 'field_name')
					],
					'description',
					'rule_name'
				],
				'defaultOrder' => [
					'name' => SORT_ASC,
				],
			],
			'pagination' => [
				'pageSize' => 50,
			],
		]);
        
        return $dataProvider;
    }
}
