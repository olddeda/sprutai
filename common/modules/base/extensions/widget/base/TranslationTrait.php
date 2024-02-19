<?php

namespace common\modules\base\extensions\widget\base;

use Yii;

/**
 * Trait for all translations
 *
 * @since 1.0.
 */
trait TranslationTrait
{
    /**
     * Yii i18n messages configuration for generating translations
     *
     * @return void
     */
    public function initI18N($dir = '') {
        if (empty($this->_messageCategory))
            return;

        if (empty($dir)) {
            $reflector = new \ReflectionClass(get_class($this));
            $dir = dirname($reflector->getFileName());
        }

        Yii::setAlias('@'.$this->_messageCategory, $dir);
        if (empty($this->i18n)) {
            $this->i18n = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@'.$this->_messageCategory.'/messages',
                'forceTranslation' => true
            ];
        }
        Yii::$app->i18n->translations[$this->_messageCategory.'*'] = $this->i18n;
    }
}