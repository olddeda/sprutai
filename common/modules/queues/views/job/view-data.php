<?php
/**
 * @var \yii\web\View $this
 * @var \common\modules\queues\records\PushRecord $record
 */

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Data';
?>
<div class="monitor-job-data">
    <?= $this->render('_table', [
        'values' => $record->getJobParams(),
    ]) ?>
</div>
