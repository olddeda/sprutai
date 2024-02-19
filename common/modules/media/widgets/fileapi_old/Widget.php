<?php

namespace common\modules\media\widgets\fileapi;

use Yii;
use yii\widgets\InputWidget;

class Widget extends InputWidget
{
    public static function t($category, $message, $params = [], $language = null) {
        return Yii::t('media-fileapi'.$category, $message, $params, $language);
    }
}
