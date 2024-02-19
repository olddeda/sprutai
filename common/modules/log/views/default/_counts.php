<?php
/**
 * @var \yii\web\View $this
 * @var \common\modules\log\Log $log
 */

use yii\helpers\Html;

/** @var \common\modules\log\Module $module */
$module = $this->context->module;

foreach ($log->getCounts() as $level => $count) {
    if (isset($module->levelClasses[$level])) {
        $class = $module->levelClasses[$level];
    } else {
        $class = $module->defaultLevelClass;
    }
    echo Html::tag('span', $count, [
        'class' => 'label ' . $class,
        'title' => $level,
    ]);
    echo ' ';
}
