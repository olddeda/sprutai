<?php

namespace common\modules\eav\handlers;

use yii\db\ActiveRecord;

use common\modules\eav\widgets\AttributeHandler;

/**
 * Class ValueHandler
 * @package common\modules\eav
 *
 * @property ActiveRecord $valueModel
 * @property string $textValue
 */
abstract class ValueHandler
{
    const STORE_TYPE_RAW = 0;
    const STORE_TYPE_OPTION = 1;
    const STORE_TYPE_MULTIPLE_OPTIONS = 2;
    const STORE_TYPE_ARRAY = 3; // Json encoded

    /** @var AttributeHandler */
    public $attributeHandler;

    /**
     * @return ActiveRecord
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getValueModel() {
        $EavModel = $this->attributeHandler->owner;

        /** @var ActiveRecord $valueClass */
        $valueClass = $EavModel->valueClass;

        $valueModel = $valueClass::findOne([
            'entityId' => $EavModel->entityModel->getPrimaryKey(),
            'attributeId' => $this->attributeHandler->attributeModel->getPrimaryKey(),
        ]);

        if (!$valueModel instanceof ActiveRecord) {
            /** @var ActiveRecord $valueModel */
            $valueModel = new $valueClass;
            $valueModel->entityId = $EavModel->entityModel->getPrimaryKey();
            $valueModel->attributeId = $this->attributeHandler->attributeModel->getPrimaryKey();
        }
        
        return $valueModel;
    }

    abstract public function defaultValue();

    abstract public function load();

    abstract public function save();

    abstract public function getTextValue();
}