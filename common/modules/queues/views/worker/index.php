<?php
/**
 * @var \yii\web\View $this
 * @var WorkerFilter $filter
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use common\modules\queues\filters\WorkerFilter;
use common\modules\queues\Module;
use common\modules\queues\records\WorkerRecord;

$this->params['breadcrumbs'][] = 'Workers';

$format = Module::getInstance()->formatter;
?>
<div class="worker-index">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $filter->search()
                ->active()
                ->with('execTotal')
                ->with('lastExec.push'),
            'sort' => [
                'attributes' => [
                    'host',
                    'pid',
                    'started_at' => [
                        'asc' => ['sender_name' => SORT_ASC, 'id' => SORT_ASC],
                        'desc' => ['sender_name' => SORT_ASC, 'id' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'started_at' => SORT_ASC,
                ],
            ],
        ]),
        'layout' => "{items}\n{pager}",
        'emptyText' => 'No workers found.',
        'tableOptions' => ['class' => 'table table-hover'],
        'formatter' => $format,
        'columns' => [
            'host',
            'pid',
            'started_at:datetime',
            'status:raw',
            'execTotalDone:integer',
            [
                'class' => ActionColumn::class,
                'template' => '{stop}',
                'buttons' => [
                    'stop' => function ($url) {
                        return Html::a(Html::icon('stop'), $url, [
                            'data' => ['method' => 'post', 'confirm' => 'Are you sure?'],
                            'title' => 'Stop the worker.',
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'stop' => function (WorkerRecord $model) {
                        return Module::getInstance()->canWorkerStop && !$model->isStopped();
                    },
                ],
            ],
        ],
        'rowOptions' => function (WorkerRecord $record) {
            if (!$record->isIdle()) {
                return ['class' => 'active'];
            }
            return [];
        },
        'beforeRow' => function (WorkerRecord $record) use ($format) {
            static $senderName;
            if ($senderName === $record->sender_name) {
                return '';
            }
            $senderName = $record->sender_name;
            $groupTitle = strtr('Sender: name (class)', [
                'name' => $record->sender_name,
                'class' => get_class(Yii::$app->get($record->sender_name)),
            ]);
            return Html::tag('tr', Html::tag('th', $format->asText($groupTitle), ['colspan' => 6]));
        },
    ]) ?>
</div>
