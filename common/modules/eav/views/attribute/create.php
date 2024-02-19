<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\eav\models\EavAttribute */

$this->title = Yii::t('eav','Create Eav Attribute');
$this->params['breadcrumbs'][] = ['label' => Yii::t('eav','title'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('eav','Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="eav-attribute-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
