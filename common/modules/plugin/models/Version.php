<?php
namespace common\modules\plugin\models;

use common\modules\base\components\Debug;
use common\modules\plugin\helpers\enum\RepositoryProvider;
use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;

use common\modules\content\helpers\enum\Status;

use common\modules\plugin\models\query\VersionQuery;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%plugin-version}}".
 *
 * @property int $id
 * @property int $plugin_id
 * @property string $version
 * @property string $url
 * @property string $text
 * @property string $data
 * @property boolean $latest
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $date_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $date
 * @property string $datetime
 *
 * Defined relations:
 * @property \common\modules\plugin\models\Plugin $plugin
 * @property \common\modules\plugin\models\VersionRepository $repository
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class Version extends ActiveRecord
{
	const TOKEN_FIELD = 'repositoryToken';
	const PROVIDER_FIELD = 'repositoryProvider';
	
	/**
	 * @var UploadedFile file attribute
	 */
	public $file;
	
	/**
	 * @var \common\modules\plugin\models\VersionRepository
	 */
	private $_repository;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%plugin_version}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['plugin_id', 'status', 'created_by', 'updated_by', 'date_at', 'created_at', 'updated_at'], 'integer'],
            [['plugin_id', 'version', 'text', 'status'], 'required'],
			[['version', 'url'], 'string', 'max' => 255],
            [['text'], 'string', 'max' => 4000],
			[['data'], 'string'],
			[['url'], 'url', 'defaultScheme' => 'https'],
            [['date', 'datetime'], 'safe'],
			[['latest'], 'boolean'],
			['version', 'unique', 'targetAttribute' => ['version', 'plugin_id'], 'message' => Yii::t('plugin-version', 'error_version_exists')],
			[['file'], 'file', 'extensions' => 'zip'],
			[['file'], 'required', 'when' => function($model) {
        		return $model->repository->provider == RepositoryProvider::MANUAL;
			}, 'enableClientValidation' => false],
			[['url'], 'required', 'when' => function($model) {
				return $model->repository->provider == RepositoryProvider::URL;
			}, 'enableClientValidation' => false],
        ];
    }
	
	/**
	 * @param $attribute
	 * @param $params
	 *
	 * @return bool
	 */
    public function rulesValidateUrl($attribute, $params) {
		$handle = curl_init($this->url);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($handle);
		$httpCode = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close($handle);
		
		if ($httpCode != 200) {
			$this->addError($attribute, Yii::t('plugin-version', 'error_url'));
			return false;
		}
		return true;
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('plugin-version', 'field_id'),
            'plugin_id' => Yii::t('plugin-version', 'field_plugin_id'),
			'version' => Yii::t('plugin-version', 'field_version'),
			'url' => Yii::t('plugin-version', 'field_url'),
            'text' => Yii::t('plugin-version', 'field_text'),
			'file' => Yii::t('plugin-version', 'field_file'),
			'latest' => Yii::t('plugin-version', 'field_latest'),
            'status' => Yii::t('plugin-version', 'field_status'),
            'date' => Yii::t('plugin-version', 'field_date'),
			'datetime' => Yii::t('plugin-version', 'field_datetime'),
            'created_by' => Yii::t('plugin-version', 'field_created_by'),
            'updated_by' => Yii::t('plugin-version', 'field_updated_by'),
            'date_at' => Yii::t('plugin-version', 'field_date_at'),
            'created_at' => Yii::t('plugin-version', 'field_created_at'),
            'updated_at' => Yii::t('plugin-version', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\plugin-version\models\query\VersionQuery the active query used by this AR class.
     */
    public static function find() {
        return new VersionQuery(get_called_class());
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPlugin() {
		return $this->hasOne(Plugin::class, ['id' => 'plugin_id'])->alias('plugin')->where([]);
	}
	
	/**
	 * @return VersionRepository
	 */
	public function getRepository() {
		if (is_null($this->_repository)) {
			$this->_repository = VersionRepository::fromArray($this->data);
		}
		return $this->_repository;
	}
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}

    /**
     * Get date formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDate($format = 'dd-MM-yyyy') {
        if (!$this->date_at)
            $this->date_at = time();
        return Yii::$app->formatter->asDate($this->date_at, $format);
    }

    /**
     * Set date
     * @param $val
     */
    public function setDate($val) {
        $val .= ' '.date('H:i:s');
        $this->date_at = strtotime($val);
    }

    /**
     * Get datetime formatted
     * @param string $format
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatetime($format = 'dd-MM-yyyy HH:mm') {
        if (!$this->date_at)
            $this->date_at = time();
        return Yii::$app->formatter->asDatetime($this->date_at, $format);
    }

    /**
     * Set datetime
     * @param $val
     */
    public function setDatetime($val) {
        $val .= ':'.date('s');
        $this->date_at = strtotime($val);
    }
	
	/**
	 * Get file path
	 * @return string
	 */
	public function getFilePath() {
		return $this->getDir('path');
	}
	
	/**
	 * Get file url
	 * @return string
	 */
	public function getFileUrl() {
		return $this->getDir('url');
	}
	
	/**
	 * Get file url
	 * @return string
	 */
	public function getFileHttp() {
		return $this->getDir('http');
	}
	
	/**
	 * Get file name
	 * @return string
	 */
	public function getFileName() {
		return $this->version;
	}
	
	/**
	 * Get file name full
	 * @return string
	 */
	public function getFile($ext = 'zip') {
		return $this->getFileName().'.'.$ext;
	}
	
	/**
	 * Check file exists
	 * @return boolean
	 */
	public function getFileExists() {
		$module = Yii::$app->getModule('media');
		return $module->fs->has($this->getFilePath().$this->getFile());
	}
	
	/**
	 * @return string
	 */
	public function getDownloadUrl() {
		if ($this->repository->provider == RepositoryProvider::URL)
			return $this->url;
		return Url::to(['download', 'id' => $this->id]);
	}
	
	/**
	 * Get dir
	 *
	 * @return array
	 */
	public function getDir($key = null) {
		$module = Yii::$app->getModule('media');
		
		if (!isset($_SERVER['HTTP_HOST']))
			$_SERVER['HTTP_HOST'] = 'sprut.ai';
		
		// Get dir split by user id
		$p = self::getUserDir('path');
		$h = '/'.self::getUserDir('http');
		$u = '/'.self::getUserDir('http');
		
		// Add model id to path
		$p.= $this->plugin_id.DIRECTORY_SEPARATOR;
		$h.= $this->plugin_id.'/';
		$u.= $this->plugin_id.'/';
		
		$tmp = [
			'path' => $p,
			'http' => $h,
			'url' => $u
		];
		
		return ($key) ? $tmp[$key] : $tmp;
	}
	
	/**
	 * Get user path
	 * @param integer $id
	 * @param string $type
	 *
	 * @return string
	 */
	public function getUserDir($type = 'path') {
		$module = Yii::$app->getModule('media');
		
		$sp = ($type == 'http') ? '/' : DIRECTORY_SEPARATOR;
		$ret = '';
		if ($type != 'path')
			$ret .= 'static'.$sp;
		$ret .= $module->fsRootPath.$sp;
		$ret .= 'version'.$sp;
		return $ret;
	}
	
	/**
	 * @param $insert
	 *
	 * @return bool
	 */
    public function beforeSave($insert) {
    	$this->data = $this->repository->toString();
		return parent::beforeSave($insert);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		// Send to moderate chat
		if ($this->oldAttributes['status'] != Status::MODERATED && isset($changedAttributes['status']) && $changedAttributes['status'] == Status::MODERATED) {
			$subject = Yii::t('notification', 'plugin_version_need_moderate_subject');
			$message = Yii::t('notification', 'plugin_version_need_moderate', [
				'url' => Url::to(['/plugin/version/update', 'plugin_id' => $this->plugin_id, 'id' => $this->id], true),
				'plugin' => $this->plugin->title,
				'version' => $this->version,
			]);
			
			Yii::$app->notification->queueTelegramIds(Yii::$app->getModule('telegram')->moderateIds, $subject, $message, 'plugin');
		}
		
		if ($this->oldAttributes['status'] == Status::MODERATED && isset($changedAttributes['status']) && $changedAttributes['status'] == Status::ENABLED) {
			$subject = Yii::t('notification', 'plugin_version_moderated_subject');
			$message = Yii::t('notification', 'pligin_version_moderated', [
				'url' => Url::to(['/plugin/default/view', 'id' => $this->id], true),
				'plugin' => $this->plugin->title,
				'version' => $this->version,
			]);
			Yii::$app->notification->queue([$this->author_id], $subject, $message, 'plugin');
		}
	}
}
