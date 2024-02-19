<?php
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model client\forms\ItunesReportForm */

$this->context->layoutContent = 'content_no_panel';

?>

<div class="panel">
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'rates')->fileInput(['multiple' => true]) ?>
        <?= $form->field($model, 'file')->fileInput(['multiple' => true]) ?>

        <button>Загрузить</button>

        <?php ActiveForm::end() ?>
    </div>
</div>

<?php if ($model->result) { ?>
<div class="panel">
    <div class="panel-body">
        <div><b>Период: </b> <?= $model->result['dates']['start'] ?> - <?= $model->result['dates']['end'] ?></div>
    <?php foreach ($model->result['items'] as $item) { ?>
        <h3><?= $item['title']?></h3>
        <hr/>
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="100">Дата</th>
                <th>Тип</th>
                <th>Количество</th>
                <th>USD</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($item['transactions'] as $t) { ?>
            <tr>
                <td><?= $t['date'] ?></td>
                <td><?= $t['type'] ?></td>
                <td><?= $t['quantity'] ?></td>
                <td><?= Yii::$app->formatter->asCurrency($t['usd'], 'USD', [
                    NumberFormatter::FRACTION_DIGITS => 2,
                ]) ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="2"><b>Итого:</b></td>
                <td><b><?= $item['quantity'] ?></b></td>
                <td><b><?= Yii::$app->formatter->asCurrency($item['usd'], 'USD', [
                   NumberFormatter::FRACTION_DIGITS => 2,
                ]) ?></b></td>
            </tr>
            </tbody>
        </table>
    <?php } ?>
    </div>
</div>
<?php } ?>
