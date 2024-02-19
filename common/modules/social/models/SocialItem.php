<?php
namespace common\modules\social\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\social\models\query\SocialItemQuery;

/**
 * This is the model class for table "{{%social_item}}".
 *
 * @property int $id
 * @property int $module_type
 * @property int $module_id
 * @property int $post_telegram_at
 * @property int $post_facebook_at
 * @property int $post_instargam_at
 * @property int $post_vk_at
 * @property int $post_ok_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class SocialItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%social_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['module_type', 'module_id', 'post_telegram_at', 'post_facebook_at', 'post_instargam_at', 'post_vk_at', 'post_ok_at', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['module_type', 'module_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('social', 'field_id'),
            'module_type' => Yii::t('social', 'field_module_type'),
            'module_id' => Yii::t('social', 'field_module_id'),
            'post_telegram_at' => Yii::t('social', 'field_post_telegram_at'),
            'post_facebook_at' => Yii::t('social', 'field_post_facebook_at'),
            'post_instargam_at' => Yii::t('social', 'field_post_instargam_at'),
            'post_vk_at' => Yii::t('social', 'field_post_vk_at'),
            'post_ok_at' => Yii::t('social', 'field_post_ok_at'),
            'created_by' => Yii::t('social', 'field_created_by'),
            'updated_by' => Yii::t('social', 'field_updated_by'),
            'created_at' => Yii::t('social', 'field_created_at'),
            'updated_at' => Yii::t('social', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\social\models\query\SocialItemQuery the active query used by this AR class.
     */
    public static function find() {
        return new SocialItemQuery(get_called_class());
    }
}
