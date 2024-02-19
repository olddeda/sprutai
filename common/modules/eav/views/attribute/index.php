<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\eav\models\EavAttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('eav','Fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('eav','EAV'), 'url' => ['/eav/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-attribute-index">

    <p>
        <?= Html::a(Yii::t('eav','Create Eav Attribute'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'entityId',
            'typeId',
            'name',
            'label',
            // 'defaultValue',
            // 'defaultOptionId',
            // 'required',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
