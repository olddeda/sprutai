<?php

use yii\helpers\Html;

$this->title = Yii::t('eav', 'title');

?>

<table class="table">
    <tr>
        <th><?= Yii::t('eav', 'Description') ?></th>
        <th><?= Yii::t('eav', 'Operations') ?></th>
    </tr>

    <tr>
        <td><?= Yii::t('eav', 'Fields constructor') ?></td>
        <td>
            <?= Html::a(Yii::t('eav', 'Create'), ['attribute/create']) ?>&nbsp;
            <?= Html::a(Yii::t('eav', 'List'), ['attribute/index']) ?>
        </td>
    </tr>

    <tr>
        <td><?= Yii::t('eav', 'Options constructor') ?></td>
        <td>
            <?= Html::a(Yii::t('eav', 'Create'), ['option/create']) ?>&nbsp;
            <?= Html::a(Yii::t('eav', 'List'), ['option/index']) ?>
        </td>
    </tr>

    <tr>
        <td><?= Yii::t('eav', 'Types constructor') ?></td>
        <td>
            <?= Html::a(Yii::t('eav', 'Create'), ['type/create']) ?>&nbsp;
            <?= Html::a(Yii::t('eav', 'List'), ['type/index']) ?>
        </td>
    </tr>

    <tr>
        <td><?= Yii::t('eav', 'Entities constructor') ?></td>
        <td>
            <?= Html::a(Yii::t('eav', 'Create'), ['entity/create']) ?>&nbsp;
            <?= Html::a(Yii::t('eav', 'List'), ['entity/index']) ?>
        </td>
    </tr>

    <tr>
        <td><?= Yii::t('eav', 'Values constructor') ?></td>
        <td>
            <?= Yii::t('eav', 'Create') ?>&nbsp;
            <?= Html::a(Yii::t('eav', 'List'), ['value/index']) ?>
        </td>
    </tr>

</table>