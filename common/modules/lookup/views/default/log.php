<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lookup\models\Lookup */

$this->title = Yii::t('lookup', 'title_log');

$this->params['breadcrumbs'][] = ['label' => Yii::t('lookup', 'title_index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('@vendor/bedezign/yii2-audit/src/views/_audit_trails', [
    'model' => $model,
    'params' => [],
]);