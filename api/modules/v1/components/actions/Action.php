<?php
namespace api\modules\v1\components\actions;

use Yii;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

use api\traits\MediaTrait;

/**
 * Class Action
 * @package api\modules\v1\components\actions
 */
class Action extends \yii\base\Action
{
    use MediaTrait;

    /**
     * @var
     */
    public $modelClass;

    /**
     * @var
     */
    public $findModel;

    /**
     * @var
     */
    public $checkAccess;

    /**
     * @var
     */
    public $uploadMedia;

    /**
     * {@inheritdoc}
     */
    public function init() {
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * @param $id
     *
     * @return mixed|ActiveRecordInterface
     * @throws NotFoundHttpException
     */
    public function findModel($id) {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        }

        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }
}
