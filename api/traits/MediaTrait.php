<?php
namespace api\traits;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\flysystem\AwsS3Filesystem;
use common\modules\base\components\flysystem\LocalFilesystem;
use common\modules\base\helpers\enum\Status;
use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\components\Image;
use common\modules\media\helpers\enum\Type;
use common\modules\media\models\MediaImage;
use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;


/**
 * Trait MediaTrait
 */
trait MediaTrait
{
    /**
     * @param ActiveRecord $model
     *
     * @return array|null
     * @throws \Exception
     */
    public function uploadMedia(ActiveRecord $model) {

        /** @var common/modules/media/Module $module */
        $module = Yii::$app->getModule('media');

        if ($model->files) {
            foreach ($model->files as $field => $media) {

                if ($behavior = $this->_findMediaBehavior($model, $field)) {

                    /** @var MediaBehavior $mediaBehavior */
                    $mediaBehavior = $model->$field;

                    $file = isset($_FILES[$field]) ? $_FILES[$field] : null;
                    if ($file) {
                        $error = null;
                        if ($file['size'] > $module->allowedMaxSize)
                            $error = Yii::t('media', 'upload_wrong_size', [Yii::$app->formatter->asBytes($module->allowedMaxSize[Type::IMAGE])]);
                        else if (!in_array($file['type'], $module->allowedTypes[Type::IMAGE]))
                            $error = Yii::t('media', 'upload_wrong_type');
                        if ($error) {
                            return [
                                'field' => $field,
                                'message' => $error
                            ];
                        }
                    }

                    $crop = ArrayHelper::getValue($media, 'crop', null);
                    $removed = ArrayHelper::getValue($media, 'removed', false);

                    $this->_saveFile($mediaBehavior, $file, $crop, $removed);

                }
            }
        }
    }

    /**
     * @param MediaBehavior $mediaBehavior
     * @param string $file
     * @param array|null $crop
     * @param bool $removed
     *
     * @return void
     */
    private function _saveFile(MediaBehavior $mediaBehavior, $file, $crop, $removed) {

        /** @var common/modules/media/Module $module */
        $module = Yii::$app->getModule('media');

        /** @var $fs AwsS3Filesystem $filesystem */
        $fs = $module->fs;

        /** @var $fs LocalFilesystem $filesystem */
        $fsLocal = Yii::$app->fsLocal;

        if ($file && file_exists($file['tmp_name'])) {
            $pathInfo = pathinfo($file['name']);

            $type = explode('/', $file['type']);
            $ext = $type[count($type) - 1];

            /** @var MediaImage $model */
            $model = $mediaBehavior->getMediaImage(true, true);

            $model->ext = $ext;
            $model->size = filesize($file['tmp_name']);

            $fileDir = $model->getFilePath(true);
            $fileDirCache = $model->getFilePath(false);
            $filePath = $fileDir.$model->getFile(false);
            $filePathOriginal = $fileDir.$model->getFileOriginal(false);

            // Recreate dir
            if ($fs->has($fileDir))
                $fs->deleteDir($fileDir);
            $fs->createDir($fileDir);

            if ($fsLocal->has($fileDirCache))
                $fsLocal->deleteDir($fileDirCache);
            $fsLocal->createDir($fileDirCache);

            // If file is image
            $fileData = file_get_contents($file['tmp_name']);
            if ($model->type == Type::IMAGE) {

                $img = new Image();
                $img->load($fileData);

                if ($crop) {
                    $x = (int)ArrayHelper::getValue($crop, 'x', 0);
                    $y = (int)ArrayHelper::getValue($crop, 'y', 0);
                    $w = (int)ArrayHelper::getValue($crop, 'width');
                    $h = (int)ArrayHelper::getValue($crop, 'height');

                    if ($w && $h) {
                        $img->cropAndScale([
                            'width' => $w,
                            'height' => $h,
                            'cropX' => $x,
                            'cropY' => $y,
                        ]);

                        $fs->write($filePath, $img->get($model->ext));
                        $fs->write($filePathOriginal, $fileData);
                    }
                }
                else {
                    $fs->write($filePath, $fileData);
                }

                $model->width = $img->getWidth();
                $model->height = $img->getHeight();
            }

            $model->status = Status::ENABLED;
            $model->save();
        }
        else if ($removed) {

            /** @var MediaImage $model */
            $model = $mediaBehavior->getMediaImage();
            if ($model) {
                $model->status = Status::DELETED;
                $model->save();
            }
        }
    }

    /**
     * @param ActiveRecord $model
     * @param string $name
     *
     * @return Behavior|null
     */
    private function _findMediaBehavior(ActiveRecord $model, string $name) {
        if ($behaviors = $model->behaviors()) {
            foreach ($behaviors as $behavior) {
                if ($behavior['class'] == MediaBehavior::class && isset($behavior['attribute']) && $behavior['attribute'] == $name) {
                    return $behavior;
                }
            }
        }
        return null;
    }

    private function _validateImage($data, $type, $fileType) {



    }

    /**
     * @param $data
     * @return false|string
     */
    private function _base64Decode($data) {
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);
            return [
                'data' => base64_decode($data),
                'type' => $type
            ];
        }
        return null;
    }

    /**
     * @param string $data
     * @return boolean
     */
    private function isBase64($data) {
        return (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) ? true : false;
    }
}