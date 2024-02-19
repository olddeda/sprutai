<?php
namespace api\modules\v1\controllers\media\actions;

use common\modules\media\components\Image;
use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\flysystem\AwsS3Filesystem;
use common\modules\base\components\flysystem\LocalFilesystem;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\models\MediaImage;
use common\modules\media\helpers\enum\Type;

use api\models\content\Content;

/**
 * Class SlimAction
 * @package api\modules\v1\controllers\media\actions
 */
class SlimAction extends Action
{

    /**
     * @param $module_type
     * @param $module_id
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function run($module_type, $module_id) {
        $result = [
            'status' => 'failure',
            'message' => Yii::t('media-upload', 'error_unknown'),
        ];

        $model = $this->_getModel($module_type, $module_id);
        if (is_null($model)) {
            throw new NotFoundHttpException('Not found');
        }

        $slim = Yii::$app->request->post('slim');
        if (is_array($slim)) {
            foreach ($slim as $s) {
                $data = Json::decode($s);

                $mediaId = ArrayHelper::getValue($data, 'meta.id');

                $mediaImage = null;

                if ($mediaId) {
                    $mediaImage = MediaImage::find()->where('module_type = :module_type AND module_id = :module_id AND id = :id AND status = :status', [
                        'module_type' => $module_type,
                        'module_id' => $module_id,
                        'id' => $mediaId,
                        'status' => Status::ENABLED,
                    ])->one();
                }

                if (is_null($mediaImage)) {
                    $mediaImage = new MediaImage();
                    $mediaImage->module_type = $module_type;
                    $mediaImage->module_id = $module_id;
                    $mediaImage->attribute = 'image';
                    $mediaImage->status = Status::TEMP;
                }

                foreach (['input', 'output'] as $name) {
                    $error = $this->_validateFile($data, $name);
                    if ($error) {
                        $result['message'] = $error;
                        return $result;
                    }
                }

                $inputFile = $_FILES[ArrayHelper::getValue($data, 'input.field')];
                $outputFile = $_FILES[ArrayHelper::getValue($data, 'output.field')];

                if (!$mediaImage->save()) {
                    $errors = $mediaImage->getFirstErrors();
                    if (count($errors)) {
                        $result['message'] = $errors[array_keys($errors)[0]];
                        return $result;
                    }
                }

                $mediaImage->status = Status::ENABLED;
                $this->_saveFile($mediaImage, $inputFile, $outputFile);

                if ($error = $this->_save($mediaImage)) {
                    $result['message'] = $error;
                    return $result;
                }

                /** @var $fs AwsS3Filesystem $filesystem */
                $fs = Yii::$app->fs;

                return [
                    'status' => 'success',
                    'id' => $mediaImage->id,
                    'width' => $mediaImage->width,
                    'height' => $mediaImage->height,
                    'name' => $mediaImage->getFile(false),
                    'path' => $fs->url.$mediaImage->getFileUrl(true),
                    'original_name' => $mediaImage->getFileOriginal(false),
                    'original_path' => $fs->url.$mediaImage->getFileUrl(true),
                ];
            }
        }

        return $result;
    }

    /**
     * @param MediaImage $mediaImage
     *
     * @return string|null
     */
    private function _save(MediaImage $mediaImage) {
        if (!$mediaImage->save()) {
            $errors = $mediaImage->getFirstErrors();
            if (count($errors)) {
                return $errors[array_keys($errors)[0]];
            }
        }
        return null;
    }

    private function _saveFile(MediaImage $mediaImage, array $inputFile, array $outputFile) {

        /** @var $fs AwsS3Filesystem $filesystem */
        $fs = Yii::$app->fs;

        /** @var $fsLocal LocalFilesystem $filesystem */
        $fsLocal = Yii::$app->fsLocal;

        $type = explode('/', $inputFile['type']);
        $ext = $type[count($type) - 1];
        list($width, $height) = getimagesize($outputFile['tmp_name']);

        $mediaImage->ext = $ext;
        $mediaImage->size = $outputFile['size'];
        $mediaImage->width = $width;
        $mediaImage->height = $height;

        $fileDir = $mediaImage->getFilePath(true);
        $fileDirCache = $mediaImage->getFilePath(false);
        $filePath = $fileDir.$mediaImage->getFile(false);
        $filePathOriginal = $fileDir.$mediaImage->getFileOriginal(false);

        if ($fs->has($fileDir))
            $fs->deleteDir($fileDir);
        $fs->createDir($fileDir);

        if ($fsLocal->has($fileDirCache))
            $fsLocal->deleteDir($fileDirCache);
        $fsLocal->createDir($fileDirCache);

        $fs->write($filePathOriginal, file_get_contents($inputFile['tmp_name']));
        $fs->write($filePath, file_get_contents($outputFile['tmp_name']));
    }

    /**
     * @param array $data
     * @param string $name
     *
     * @return string|null
     */
    private function _validateFile(array $data, string $name) {
        Yii::$app->formatter->sizeFormatBase = 1000;

        $field = ArrayHelper::getValue($data, $name.'.field');

        if (!$field || !isset($_FILES[$field]) || $_FILES[$field]['error']) {
            return Yii::t('media-slim', 'status_not_sent_'.$name);
        }

        $file = $_FILES[$field];
        if ($file['size'] > $this->getModule()->allowedMaxSize[Type::IMAGE]) {
            return Yii::t('media-slim', 'status_file_size', [Yii::$app->formatter->asShortSize($this->getModule()->allowedMaxSize[Type::IMAGE])]);
        }
        if (!in_array($file['type'], $this->getModule()->allowedTypes[Type::IMAGE])) {
            $types = [];
            foreach ($this->getModule()->allowedTypes[Type::IMAGE] as $type) {
                $types[] = mb_strtolower(str_replace('image/', '', $type));
            }
            return Yii::t('media-slim', 'status_file_type', implode(', ', $types));
        }

        list($width, $height) = getimagesize($file['tmp_name']);
        $min = $this->getModule()->allowedImageResolution['min'];
        if ($width < $min || $height < $min) {
            return Yii::t('media-slim', 'status_file_small', [$min]);
        }
        $max = $this->getModule()->allowedImageResolution['max'];
        if ($width > $max || $height > $max) {
            return Yii::t('media-slim', 'status_file_big', $max);
        }

        return null;
    }

    /**
     * @param $module_type
     * @param $module_id
     *
     * @return array|ActiveRecord|null
     */
    private function _getModel($module_type, $module_id) {
        $model = null;

        switch ($module_type) {
            case ModuleType::CONTENT:
                return Content::findOwn($module_id, true, 'content');
        }

        return $model;
    }

    /**
     * return common/modules/media/Module
     */
    private function getModule() {
        return Yii::$app->getModule('media');
    }
}