<?php
namespace common\modules\media\models;

use Yii;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\user\models\User;

use common\modules\media\Module;
use common\modules\media\models\query\MediaQuery;
use common\modules\media\helpers\enum\Type;
use common\modules\media\helpers\enum\Mode;

/**
 * This is the model class for table "{{%media}}".
 *
 * @property integer $id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $type
 * @property string $attribute
 * @property string $title
 * @property string $alt
 * @property string $descr
 * @property string $ext
 * @property integer $is_main
 * @property integer $width
 * @property integer $height
 * @property integer $size
 * @property integer $sequence
 * @property integer $status
 * @property array $data
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $updatedBy
 * @property User $createdBy
 *
 * @property \common\modules\media\Module $module
 */
class Media extends ActiveRecord
{
	const NAME_IMAGE	        = 'image';
	const NAME_IMAGE_BACKGROUND	= 'image_background';
	const NAME_VIDEO	        = 'video';
	const NAME_FILE		        = 'file';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%media}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['module_type', 'module_id', 'status'], 'required'],
            [['module_type', 'module_id', 'type', 'width', 'height', 'size', 'sequence', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['is_main'], 'boolean'],
            [['descr'], 'string'],
            [['title', 'alt', 'attribute'], 'string', 'max' => 255],
			[['ext'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('media', 'field_id'),
            'module_type' => Yii::t('media', 'field_module_type'),
            'module_id' => Yii::t('media', 'field_module_id'),
            'type' => Yii::t('media', 'field_type'),
            'title' => Yii::t('media', 'field_title'),
			'alt' => Yii::t('media', 'field_alt'),
            'descr' => Yii::t('media', 'field_descr'),
			'ext' => Yii::t('media', 'field_ext'),
            'width' => Yii::t('media', 'field_width'),
            'height' => Yii::t('media', 'field_height'),
			'width_and_height' => Yii::t('media', 'field_width_and_height'),
            'size' => Yii::t('media', 'field_size'),
			'sequence' => Yii::t('media', 'field_sequence'),
			'is_main' => Yii::t('media', 'field_is_main'),
            'status' => Yii::t('media', 'field_status'),
            'created_by' => Yii::t('media', 'field_created_by'),
            'updated_by' => Yii::t('media', 'field_updated_by'),
            'created_at' => Yii::t('media', 'field_created_at'),
            'updated_at' => Yii::t('media', 'field_updated_at'),
			'image' => Yii::t('media', 'field_image'),
        ];
    }

	/**
	 * @inheritdoc
	 * @return \common\modules\media\models\query\MediaQuery the active query used by this AR class.
	 */
	public static function find() {
		return new MediaQuery(get_called_class());
	}

	/**
	 * Find own model
	 *
	 * @param integer $id
	 * @param bool|false $except
	 * @param string $messageCategory
	 * @param array $relations
	 *
	 * @return array|mixed|\yii\db\ActiveRecord|null
	 * @throws \Throwable
	 */
	static public function findOwn($id, $except = false, $messageCategory = 'media', $relations = [], $cache = false) {
		$class = get_called_class();
		$query = $class::find();
		$query->where = [];

		$query->andWhere($class::tableName().'.id = :id', [
			':id' => $id
		]);

		if (count($relations))
			$query->joinWith($relations);

		// Add owner user condition
		if (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin) {
			$query->andWhere($class::tableName().'.created_by = :created_by AND '.$class::tableName().'.status = :status', [
				':created_by' => Yii::$app->user->id,
				':status' => Status::ENABLED,
			]);
		}

		$model = null;
		if ($cache) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.$class::tableName();

			$model = self::getDb()->cache(function ($db) use($query) {
				return $query->one();
			}, Yii::$app->params['cache.duration'], $dependency);
		}
		else
			$model = $query->one();

		if ($model === null && $except)
			throw new NotFoundHttpException(Yii::t($messageCategory, 'error_not_exists'));

		return $model;
	}

	/**
	 * Get created user model
	 * @return \common\modules\user\models\query\UserQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}

    /**
	 * Get updated user model
     * @return \common\modules\user\models\query\UserQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

	/**
	 * Get dir
	 *
	 * @return array
	 */
	public function getDir($isOriginal = false, $key = null) {
		$host = $this->module->hostScheme.'://'.$this->module->hostName;
		if ($isOriginal)
			$host = $this->module->fs->url;
		
		// Get dir split by user id
		$p = self::getUserDir($this->created_by, 'path', $isOriginal);
		$h = $host.'/'.self::getUserDir($this->created_by, 'http', $isOriginal);
		$u = '/'.self::getUserDir($this->created_by, 'http', $isOriginal);

		// Add module type to path
		$p.= $this->module_type.DIRECTORY_SEPARATOR;
		$h.= $this->module_type.'/';
		$u.= $this->module_type.'/';

		// Add module id to path
		$p.= $this->module_id.DIRECTORY_SEPARATOR;
		$h.= $this->module_id.'/';
		$u.= $this->module_id.'/';

		// Add model id to path
		$p.= $this->id.DIRECTORY_SEPARATOR;
		$h.= $this->id.'/';
		$u.= $this->id.'/';
		
		$tmp = [
			'path' => $p,
			'http' => $h,
			'url' => $u
		];
		
		return ($key) ? $tmp[$key] : $tmp;
	}
	
	/**
	 * @return null|static
	 */
	public function getModule($name = null) {
		return Module::getInstance();
	}

	/**
	 * Get file path
	 * @return string
	 */
	public function getFilePath($isOriginal = false) {
		return $this->getDir($isOriginal, 'path');
	}

	/**
	 * Get file url
	 * @return string
	 */
	public function getFileUrl($isOriginal = false) {
		return $this->getDir($isOriginal, 'url');
	}

	/**
	 * Get file url
	 * @return string
	 */
	public function getFileHttp($isOriginal = false) {
		return $this->getDir($isOriginal, 'http');
	}

	/**
	 * Get file name
	 * @return string
	 */
	public function getFileName() {
		return $this->attribute;
	}

	/**
	 * Get file name full
	 * @return string
	 */
	public function getFile($appendTime = true) {
		$file = $this->getFileName().'.'.$this->ext;
		if ($appendTime && $this->updated_at)
			$file .= '?'.$this->updated_at;
		return $file;
	}

    /**
     * Get file name full
     * @return string
     */
    public function getFileOriginal($appendTime = true) {
        $file = 'original.'.$this->ext;
        if ($appendTime && $this->updated_at)
            $file .= '?'.$this->updated_at;
        return $file;
    }

	/**
	 * Check file exists
	 * @return boolean
	 */
	public function getFileExists() {
	    return true;
	    
		if ($this->status != Status::ENABLED)
			return false;
		
		if ($this->module->fs instanceof \creocoder\flysystem\AwsS3Filesystem)
			return true;
		
		return $this->module->fs->has($this->getFilePath(true).$this->getFile());
	}

	/**
	 * Get image src
	 * @param integer $width
	 * @param integer $height
	 * @param integer $mode
	 * @param boolean $watermark
	 * @param boolean $useTimestamp
	 *
	 * @return string
	 */
	public function getImageSrc($width = null, $height = null, $mode = Mode::CROP_CENTER, $watermark = false, $useTimestamp = true, $isOriginal = false) {
		$src = $this->getFileHttp($isOriginal);
		if ($width && $height)
			$src .= MediaFormat::format($width, $height, $mode, $watermark).'_';
		$src .= $this->getFile($useTimestamp);
		return $src;
	}

	/**
	 * Get user path
	 * @param integer $id
	 * @param string $type
	 *
	 * @return string
	 */
	public function getUserDir($id, $type = 'path', $isOriginal = false) {
		$sp = ($type == 'http') ? '/' : DIRECTORY_SEPARATOR;
		$ret = '';
		
		if ($type != 'path') {
			if (!$isOriginal)
				$ret .= 'static'.$sp;
		}
		$ret .= $this->module->fsRootPath.$sp;
		$ret .= ($isOriginal) ? $this->module->fsOriginalPath : $this->module->fsCachePath;
		$ret .= $sp;
		$ret .= self::getSplitPath($id, $type);
		
		return $ret;
	}

	/**
	 * Split path by id
	 * @param integer $id
	 * @param string $type
	 *
	 * @return string
	 */
	static public function getSplitPath($id, $type = 'filesys') {
		$sp = ($type == 'http') ? '/' : DIRECTORY_SEPARATOR;
		if (strlen($id) < 6)
			$id = str_repeat('0', 6 - strlen($id)).$id;
		$path = substr($id, -6);
		return $path{0}.$path{1}.$sp.$path{2}.$path{3}.$sp.$path{4}.$path{5}.$sp;
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		
		if (isset($changedAttributes['is_main']) && $changedAttributes['is_main']) {
			self::updateAll(['is_main' => false], [
				'and',
				['<>', 'id', $this->id],
				['module_type' => $this->module_type, 'module_id' => $this->module_id, 'type' => $this->type, 'attribute' => $this->attribute],
			]);
		}
		
		return parent::afterSave($insert, $changedAttributes);
	}
	
	public function delete($useStatus = true) {
		if (!$useStatus) {
			
			/** @var common/modules/media/Module $module */
			$module = Yii::$app->getModule('media');
			
			/** @var \creocoder\flysystem\LocalFilesystem $filesystem */
			$fs = $module->fs;
			
			$fileDir = $this->getFilePath(true);
			$fileDirCache = $this->getFilePath(false);
			
			// Recreate dir
			if ($fs->has($fileDir))
				$fs->deleteDir($fileDir);
			if ($fs->has($fileDirCache))
				$fs->deleteDir($fileDirCache);
		}
		
		parent::delete($useStatus);
	}
}
