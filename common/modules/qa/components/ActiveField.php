<?php
namespace common\modules\qa\components;

use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class ActiveField
 * @package common\modules\qa\components
 */
class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * Makes field auto completable
     * @param array $route auto complete route
     * @return static the field object itself
     */
    public function autoComplete($route)
    {
        static $counter = 0;

        $this->inputOptions['class'] .= ' typeahead typeahead-' . (++$counter);

        $data['remote'] = Url::toRoute($route) . '?q=%QUERY';

        $this->form->getView()->registerJs("yii.qa.fieldAutocomplete($counter, " . Json::encode($data) . ");");

        return $this;
    }
}
