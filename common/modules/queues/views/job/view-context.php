<?php
/**
 * @var \yii\web\View $this
 * @var \common\modules\queues\records\PushRecord $record
 */

use common\modules\queues\Module;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Environment';

$format = Module::getInstance()->formatter;
?>
<div class="monitor-job-env">
    <h3>Push Trace</h3>
    <pre><?= $record->trace ?></pre>
    <h3>Push Context</h3>
    <pre><?= $record->context ?></pre>
</div>
