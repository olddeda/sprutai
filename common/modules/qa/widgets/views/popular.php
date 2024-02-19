<?php
/**
 * @var common\modules\qa\models\Question[] $models
 */
use common\modules\qa\Module;
use yii\helpers\Html;

?>

<div class="panel panel-default">
    <div class="panel-heading"><?= Module::t('main', 'Popular Questions') ?></div>
    <ul class="qa-questions-list list-group">
        <?php if (!empty($models)): ?>
            <?php foreach ($models as $model): ?>
                <li class="list-group-item">
                    <a href="<?= Module::url(['/qa/default/view', 'id' => $model->id, 'alias' => $model->alias]) ?>"
                       title="<?= Html::encode($model->title) ?>">
                        <?= Html::encode($model->title) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item"><?= Module::t('main', 'No popular questions') ?></li>
        <?php endif; ?>
    </ul>
</div>

