<?php
namespace common\modules\notification\models;

use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use common\modules\base\components\ActiveRecord;

/**
 * This is the model class for table "{{%notification}}".
 *
 * @property integer $id
 * @property integer $from_id
 * @property integer $to_id
 * @property string $event
 * @property string $title
 * @property string $message
 * @property string $params
 * @property integer $created_at
 * @property integer $updated_at
 */
class Notification extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
			[['from_id', 'to_id', 'created_at', 'updated_at'], 'integer'],
			[['message', 'params'], 'string'],
          	[['title'], 'string', 'max' => 255],
          	[['event'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
          'id' => Yii::t('notification', 'field_id'),
          'from_id' => Yii::t('notification', 'field_from_id'),
          'to_id' => Yii::t('notification', 'field_to_id'),
          'event' => Yii::t('notification', 'field_event'),
          'title' => Yii::t('notification', 'field_title'),
          'message' => Yii::t('notification', 'field_message'),
          'params' => Yii::t('notification', 'field_params'),
          'created_at' => Yii::t('notification', 'field_created_at'),
          'updated_at' => Yii::t('notification', 'field_updated_at'),
        ];
    }

    /**
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function messages($where = []) {
        if (!$where) {
            $where = ['or', 'to_id' => Yii::$app->user->identity->id, 'from_id' => Yii::$app->user->identity->id];
        }
        return self::find()->where($where)->all();
    }

    /**
     * @param array $params
     */
    public function setParams($params = []){
        $params = ArrayHelper::merge($this->attributes, $params);
        $this->params = Json::encode($params);
    }

    /**
     * @return array|mixed
     */
    public function getParams(){
        $params = Json::decode($this->getAttribute('params'));
        if (!$params)
            $params = [];
        return $params;
    }

    /**
     * @param string $name
     * @return array|mixed
     * @throws Exception
     */
    public function __get($name) {

        if($name == 'attributes'){
            return $this->getAttributes();
        }

        // If name is model attribute
        $attributes = $this->attributes();
        if(in_array($name, $attributes)){
            return parent::__get($name);
        }

        // If name is param of model`s attribute by name params
        $params = $this->getParams();
        if (isset($params[$name])) {
            return $params[$name];
        }

        throw new Exception();
    }
}
