<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tagModel common\modules\tag\models\Tag */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('plugin', 'title').' - '.$tagModel->title;

$this->params['breadcrumbs'][] = ['url' => Url::toRoute('plugins/index'), 'label' => Yii::t('plugin', 'title')];
$this->params['breadcrumbs'][] = Html::encode($this->title);

?>

<?= $this->render('_index', [
	'dataProvider' => $dataProvider,
]) ?>
