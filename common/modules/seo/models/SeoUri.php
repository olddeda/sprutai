<?php
namespace common\modules\seo\models;

use common\modules\base\components\Debug;
use Yii;
use yii\db\Query;
use yii\caching\DbDependency;

use common\modules\base\components\ActiveRecord;

use common\modules\seo\Module;
use common\modules\seo\models\query\SeoUriQuery;

/**
 * This is the model class for table "{{%seo_uri}}".
 *
 * @property integer $id
 * @property integer $module_type
 * @property integer $module_id
 * @property string $module_route
 * @property string $module_params
 * @property string $uri
 * @property integer $created_at
 * @property integer $updated_at
 *
 * * Defined relations:
 * @property \common\modules\seo\models\SeoUriHistory[] $seoUriHistory
 *
 */
class SeoUri extends ActiveRecord
{
	/**
	 * @var
	 */
	static private $_uris;
	
	/**
	 * @var
	 */
	static private $_moduleParams;
	
	/**
	 * @var string
	 */
	private $_uri_old;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%seo_uri}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'module_id', 'module_route', 'uri'], 'required'],
			[['module_type', 'module_id', 'created_at', 'updated_at'], 'integer'],
			[['module_route'], 'string', 'max' => 255],
			[['module_params'], 'string', 'max' => 4000],
			[['uri'], 'string', 'max' => 1000],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('seo-uri', 'field_'),
			'module_type' => Yii::t('seo-uri', 'field_module_type'),
			'module_id' => Yii::t('seo-uri', 'field_module_id'),
			'module_route' => Yii::t('seo-uri', 'field_module_route'),
			'module_params' => Yii::t('seo-uri', 'field_module_params'),
			'uri' => Yii::t('seo-uri', 'field_uri'),
			'created_at' => Yii::t('seo-uri', 'field_created_at'),
			'updated_at' => Yii::t('seo-uri', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\seo\models\query\SeoUriQuery the active query used by this AR class.
	 */
	public static function find() {
		return new SeoUriQuery(get_called_class());
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSeoUriHistory() {
		return $this->hasMany(SeoUriHistory::className(), ['seo_uri_id' => 'id']);
	}
	
	/**
	 * Get uri for module action
	 *
	 * @param $moduleClass
	 * @param int $moduleId
	 * @param string $action
	 *
	 * @return string
	 */
	static public function uriForModule($moduleClass, $moduleId = 0, $action = 'index') {
		return self::uriForRoute($moduleClass.'/'.$action.'/'.$moduleId);
	}
	
	/**
	 * Get uri for route
	 * @param string $route
	 * @return string
	 */
	static public function uriForRoute($route) {
		if (!self::$_uris) {
			self::$_uris = Yii::$app->cache->get('uri_for_route');
			if (!self::$_uris) {
				
				// Fetch data
				self::$_uris = self::getDb()->createCommand("SELECT CONCAT_WS('/', module_route, module_id), TRIM(BOTH '/' FROM uri) FROM ".self::tableName())->queryAll(\PDO::FETCH_KEY_PAIR);
				
				// Create dependency
				$dependency = new DbDependency();
				$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
				
				// Set cache
				Yii::$app->cache->set('uri_for_route', self::$_uris, Yii::$app->params['cache.duration'], $dependency);
			}
		}
		
		return (self::$_uris && is_array(self::$_uris) && isset(self::$_uris[$route])) ? self::$_uris[$route] : null;
	}
	
	/**
	 * Get params for route
	 * @param string $route
	 * @return array
	 */
	static public function moduleParamsForRoute($route) {
		if (!self::$_moduleParams) {
			self::$_moduleParams = Yii::$app->cache->get('uri_params_for_route');
			if (!self::$_moduleParams) {
				
				// Fetch data
				self::$_moduleParams = self::getDb()->createCommand("SELECT CONCAT_WS('/', module_route, module_id), module_params FROM ".self::tableName())->queryAll(\PDO::FETCH_KEY_PAIR);
				
				// Create dependency
				$dependency = new DbDependency();
				$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
				$dependency->db = self::getDb();
				
				// Set cache
				//Yii::$app->cache->set('uri_for_route', self::$_uris, Yii::$app->params['cache.duration'], $dependency);
			}
		}
		return (self::$_moduleParams && is_array(self::$_moduleParams) && isset(self::$_moduleParams[$route]) && is_string(self::$_moduleParams[$route]) && strlen(self::$_moduleParams[$route])) ? unserialize(self::$_moduleParams[$route]) : array();
	}
	
	/**
	 * Save uri
	 * @param $moduleType
	 * @param $moduleId
	 * @param $moduleRoute
	 * @param $moduleParams
	 * @param $uri
	 */
	static public function saveUri($moduleType, $moduleId, $moduleRoute, $moduleParams, $uri) {
		$model = SeoUri::find()->where('module_type = :module_type AND module_id = :module_id', [
			':module_type' => $moduleType,
			':module_id' => $moduleId,
		])->one();
		if (is_null($model)) {
			$model = new SeoUri;
			$model->module_type = $moduleType;
			$model->module_id = $moduleId;
		}
		$model->module_route = strtolower($moduleRoute);
		$model->module_params = ($moduleParams && is_array($moduleParams)) ? serialize($moduleParams) : serialize([]);
		$model->uri = '/'.trim($uri, '/').'/';
		$model->save();
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		
		// Set old uri
		$this->_uri_old = $this->uri;
		
		parent::afterFind();
	}
	
	/**
	 * @param bool $insert
	 * @param array $changedAttributes
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		// Save old uri
		if ($this->_uri_old && $this->_uri_old !== $this->uri) {
			
			if (!SeoUriHistory::find()->where('uri = :uri', [
				':uri' => $this->_uri_old,
			])->exists()) {
				$model = new SeoUriHistory;
				$model->seo_uri_id = $this->id;
				$model->uri = $this->_uri_old;
				$model->save();
			}
			
			// Get count
			$query = new Query;
			$query->select('COUNT(*)')->from(self::tableName())->where(['like', 'uri', $this->_uri_old]);
			$count = $query->createCommand(self::getDb())->queryScalar();
			if ($count) {
				
				self::getDb()->createCommand('
					INSERT INTO '.SeoUriHistory::tableName().'	(seo_uri_id, uri, created_at, updated_at)
					SELECT id, uri, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
					FROM '.self::tableName().' AS so
					WHERE so.uri LIKE :uri_like
					AND NOT EXISTS (SELECT uri FROM '.SeoUriHistory::tableName().' WHERE uri = so.uri)
				', [
					':uri_like' => '%'.$this->_uri_old.'%',
				])->execute();
				
				self::getDb()->createCommand('
					UPDATE '.self::tableName().'
					SET uri = REPLACE(uri, :uri_old, :uri_new)
					WHERE id != :id
				', [
					':uri_old' => $this->_uri_old,
					':uri_new' => $this->uri,
					':id' => $this->id,
				])->execute();
			}
		}
	}
}