<?php

namespace common\modules\eav;


use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;

use common\modules\eav\models\EavAttribute;

/**
 * Class EavBehavior
 * @package common\modules\eav
 *
 * @mixin ActiveRecord
 * @property EavModel $eav;
 * @property ActiveRecord $owner
 */
class EavBehavior extends Behavior
{
    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];

    }

    /** @var array */
    public $valueClass;

    protected $EavModel;

    public function init() {
        assert(isset($this->valueClass));
    }

    /**
     * @return EavModel
     */
    public function __get($name = '') {
        return $this->EavModel = EavModel::create([
            'entityModel' => $this->owner,
            'valueClass' => $this->valueClass,
            'attribute' => $name,
        ]);

    }

    public function canGetProperty($name, $checkVars = true) {
        return EavAttribute::find()->where(['name' => $name])->exists();
    }

    public function beforeValidate() {
        static $running;

        if (empty($running)) {
            $running = true;

            $attributeNames = $this->owner->activeAttributes();

            foreach ($attributeNames as $attributeName){
                if(preg_match('~c\d+~', $attributeName)){
                    if(!EavAttribute::find()->where(['name' => $attributeName])->exists()){
                        throw new Exception(\Yii::t('eav', 'Attribute {name} not found', ['name' => $attributeName]));
                    }
                }
            }

            return $this->owner->validate();
        }

        $running = false;
    }

    public function beforeSave() {
    }

    public function getLabel($attribute) {
        return EavAttribute::find()->select(['label'])->where(['name' => $attribute])->scalar();
    }

    public function afterSave() {
        if(\Yii::$app->request->isPost){
            $this->eav->save(false);
        }

    }

}