<?php

namespace common\modules\base\extensions\widget\base;

/**
 * Base module class
 */
class Module extends \yii\base\Module
{
    use TranslationTrait;

    /**
     * @var array the the internalization configuration for this widget
     */
    public $i18n = [];

    /**
     * @var string translation message file category name for i18n
     */
    protected $_messageCategory = '';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->initI18N();
    }
}
