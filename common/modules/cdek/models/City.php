<?php
namespace common\modules\cdek\models;

use common\modules\base\components\ActiveRecord;

use common\modules\cdek\models\query\CityQuery;

/**
 * This is the model class for table "{{%cdek_city}}".
 *
 * @property integer $id
 * @property integer country_code
 * @property integer post_code
 * @property integer city_dd
 * @property string full_name
 * @property string full_name_eng
 * @property string name
 * @property string name_eng
 * @property string country
 * @property string country_en
 * @property string region
 * @property string region_en
 * @property string fias
 * @property string fias_full_name
 * @property string kladr
 * @property string pvz_code
 * @property string $title
 * @property string $url
 *
 */
class City extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%cdek_city}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['country_code', 'post_code', 'city_dd'], 'integer'],
			[['full_name', 'full_name_eng', 'name', 'name_eng', 'country', 'country_en', 'region', 'region_en', 'fias', 'fias_full_name', 'kladr', 'pvz_code'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 * @return CityQuery the active query used by this AR class.
	 */
	public static function find() {
		return new CityQuery(get_called_class());
	}
}
