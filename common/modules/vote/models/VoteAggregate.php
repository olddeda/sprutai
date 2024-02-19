<?php

namespace common\modules\vote\models;

use Yii;

use common\modules\base\components\ActiveRecord;

/**
 * This is the model class for table "vote_aggregate".
 *
 * @package common\modules\vote\models
 * @property integer $id
 * @property integer $entity
 * @property integer $entity_id
 * @property integer $positive
 * @property integer $negative
 * @property float $rating
 * @property integer $created_at
 * @property integer $updated_at
 */
class VoteAggregate extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName() {
        return '{{%vote_aggregate}}';
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            [['entity', 'entity_id', 'positive', 'negative', 'rating'], 'required'],
            [['entity', 'entity_id', 'positive', 'negative', 'created_at', 'updated_at'], 'integer'],
            [['rating'], 'number']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('vote_aggregate', 'field_id'),
            'entity' => Yii::t('vote_aggregate', 'field_entity'),
            'entity_id' => Yii::t('vote_aggregate', 'field_entity_id'),
            'positive' => Yii::t('vote_aggregate', 'field_positive'),
            'negative' => Yii::t('vote_aggregate', 'field_negative'),
            'rating' => Yii::t('vote_aggregate', 'field_rating'),
        ];
    }
}
