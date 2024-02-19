<?php

/**
 * @var \yii\web\View $this
 * @var \common\modules\queues\records\PushRecord $record
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;

use common\modules\queues\Module;
use common\modules\queues\records\ExecRecord;
use common\modules\queues\widgets\LinkPager;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = 'Attempts';

$format = Module::getInstance()->formatter;
?>
<div class="monitor-job-attempts">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $record->getExecs(),
            'sort' => [
                'attributes' => [
                    'attempt',
                ],
                'defaultOrder' => [
                    'attempt' => SORT_DESC,
                ],
            ],
        ]),
        'layout' => "{items}\n{pager}",
        'pager' => [
            'class' => LinkPager::class,
        ],
        'emptyText' => 'No workers found.',
        'tableOptions' => ['class' => 'table table-hover'],
        'formatter' => $format,
        'columns' => [
            'attempt:integer',
            'started_at:datetime:Started',
            'finished_at:time:Finished',
            'duration:duration',
            'memory_usage:shortSize',
            'retry:boolean',
        ],
        'rowOptions' => function (ExecRecord $record) {
            $options = [];
            if ($record->isFailed()) {
                Html::addCssClass($options, 'danger');
            }
            return $options;
        },
        'afterRow' => function (ExecRecord $record) use ($format) {
            if ($record->isFailed()) {
                return strtr('<tr class="error-line danger text-danger"><td colspan="6">{error}</td></tr>', [
                    '{error}' => $format->asNtext($record->error),
                ]);
            }
            return '';
        },
    ]) ?>
</div>
<?php
$this->registerCss(
<<<CSS
tr.error-line > td {
    white-space: normal;
    word-break: break-all;
}
CSS
);
