<?php

namespace common\modules\media\models;

use Yii;
use yii\caching\DbDependency;
use yii\web\HttpException;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;

use common\modules\media\models\query\MediaFormatQuery;
use common\modules\media\helpers\enum\Mode;

/**
 * This is the model class for table "{{%media_format}}".
 *
 * @property integer $id
 * @property integer $width
 * @property integer $height
 * @property integer $mode
 * @property integer $watermark
 * @property string $format
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user_created_by
 * @property User $user_updated_by
 */
class MediaFormat extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%media_format}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['width', 'status'], 'required'],
            [['width', 'height', 'mode', 'watermark', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['format'], 'string', 'max' => 20],
            [['format'], 'unique'],
	        //['height', 'required', 'when' => function($model) {
        	//    return ($model->mode == Mode::RESIZE_WIDTH) ? false : true;
	        //}],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('media-format', 'field_id'),
            'width' => Yii::t('media-format', 'field_width'),
            'height' => Yii::t('media-format', 'field_height'),
            'mode' => Yii::t('media-format', 'field_mode'),
			'watermark' => Yii::t('media-format', 'field_watermark'),
            'format' => Yii::t('media-format', 'field_format'),
            'status' => Yii::t('media-format', 'field_status'),
            'created_by' => Yii::t('media-format', 'field_created_by'),
            'updated_by' => Yii::t('media-format', 'field_updated_by'),
            'created_at' => Yii::t('media-format', 'field_created_at'),
            'updated_at' => Yii::t('media-format', 'field_updated_at'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\modules\media\models\query\MediaFormatQuery the active query used by this AR class.
     */
    public static function find() {
        return new MediaFormatQuery(get_called_class());
    }

	static public function get($format) {

		// Create dependency by max updated_at
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();

		// Get all formats
		$formats = Yii::$app->db->cache(function ($db) {
			$tmp = [];
			$models =  self::find()->where('status = :status', [
				':status' => Status::ENABLED,
			])->all();
			if ($models) {
				foreach ($models as $model)
					$tmp[$model->format] = $model;
			}
			return $tmp;
		}, Yii::$app->params['cache.duration'], $dependency);

		if (!isset($formats[$format])) {
		    die('Format '.$format.' not found');
            throw new HttpException(500, 'Format '.$format.' not found');
        }

		return $formats[$format];
	}

	/**
	 * Get created user model
	 * @return \common\modules\user\models\User
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::className(), ['id' => 'created_by']);
	}

	/**
	 * Get updated user model
	 * @return \common\modules\user\models\User
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::className(), ['id' => 'updated_by']);
	}

	/**
	 * Generate image format string
	 * @param $width
	 * @param $height
	 * @param $mode
	 * @param bool|false $watermark
	 *
	 * @return string
	 */
	public static function format($width, $height, $mode, $watermark = false) {
		$postfix = '';
		if ($mode == Mode::CROP_TOP)
			$postfix = 't';
		else if ($mode == Mode::CROP_CENTER)
			$postfix = 'c';

		if ($watermark)
			$postfix .= 'w';
		
		$tmp = [];
		$tmp[] = $width;
		
		if ($mode != Mode::RESIZE_WIDTH)
			$tmp[] = $height;
		
		$tmp[] = $postfix;

		return implode('x', $tmp);
	}

	/**
	 * @inheritdoc
	 *
	 * @return bool
	 */
	public function beforeSave($insert) {
		if (!$this->height)
			$this->height = 0;

		// Generate format field
		$this->format = self::format($this->width, $this->height, $this->mode, $this->watermark);

		return parent::beforeSave($insert);
	}
}
