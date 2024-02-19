<?php
namespace common\modules\seo\models;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\ActiveRecord;

use common\modules\seo\Module;
use common\modules\seo\models\query\SeoUriHistoryQuery;

/**
 * This is the model class for table "{{%seo_uri_history}}".
 *
 * @property integer $id
 * @property integer $seo_uri_id
 * @property string $uri
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\seo\models\SeoUri $seoUri
 */
class SeoUriHistory extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%seo_uri_history}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['seo_uri_id', 'uri', ], 'required'],
			[['seo_uri_id', 'created_at', 'updated_at'], 'integer'],
			[['uri'], 'string', 'max' => 1000],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('seo-uri-history', 'field_id'),
			'seo_uri_id' => Yii::t('seo-uri-history', 'field_seo_uri_id'),
			'uri' => Yii::t('seo-uri-history', 'field_uri'),
			'created_at' => Yii::t('seo-uri-history', 'field_created_at'),
			'updated_at' => Yii::t('seo-uri-history', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\seo\models\query\SeoUriHistoryQuery the active query used by this AR class.
	 */
	public static function find() {
		return new SeoUriHistoryQuery(get_called_class());
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSeoUri() {
		return $this->hasOne(SeoUri::className(), ['id' => 'seo_uri_id']);
	}
}