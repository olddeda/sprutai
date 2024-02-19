<?php

/**
 * @var \yii\web\View $this
 * @var \common\modules\queues\records\PushRecord $record
 */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;

use common\modules\queues\filters\JobFilter;
use common\modules\queues\Module;

$this->params['breadcrumbs'][] = [
    'label' => 'Jobs',
    'url' => ['index'],
];
if ($filtered = JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = [
        'label' => 'Filtered',
        'url' => ['index'] + $filtered,
    ];
}
if ($parent = $record->parent) {
    $this->params['breadcrumbs'][]  = [
        'label' => "#$parent->job_uid",
        'url' => [Yii::$app->requestedAction->id, 'id' => $parent->id],
    ];
}
$this->params['breadcrumbs'][]  = [
    'label' => "#$record->job_uid",
    'url' => [Yii::$app->requestedAction->id, 'id' => $record->id],
];

$module = Module::getInstance();
?>
<div class="pull-right">
    <?= !$module->canExecStop ? '' : Html::a(
        Html::icon('stop') . ' Stop',
        ['stop', 'id' => $record->id],
        [
            'title' => 'Mark as stopped.',
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
            'disabled' => !$record->canStop(),
            'class' => 'btn btn-' . ($record->canStop() ? 'danger' : 'default'),
        ]
    ) ?>
    <?= !$module->canPushAgain ? '' : Html::a(
        Html::icon('repeat') . ' Push Again',
        ['push', 'id' => $record->id],
        [
            'title' => 'Push again.',
            'data' => [
                'method' => 'post',
                'confirm' => 'Are you sure?',
            ],
            'disabled' => !$record->canPushAgain(),
            'class' => 'btn btn-' . ($record->canPushAgain() ? 'primary' : 'default'),
        ]
    ) ?>
</div>
<?= Nav::widget([
    'options' => ['class' =>'nav nav-tabs'],
    'items' => [
        [
            'label' => 'Details',
            'url' => ['job/view-details', 'id' => $record->id],
        ],
        [
            'label' => 'Context',
            'url' => ['job/view-context', 'id' => $record->id],
        ],
        [
            'label' => 'Data',
            'url' => ['job/view-data', 'id' => $record->id],
        ],
        [
            'label' => "Attempts ($record->attemptCount)",
            'url' => ['job/view-attempts', 'id' => $record->id],
        ],
    ],
]) ?>
