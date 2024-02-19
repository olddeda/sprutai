<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\base\extensions\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */

?>

<?= Nav::widget([
	'options' => ['class' => 'nav navbar-nav tabs'],
	'activateParents' => true,
	'labelTag' => false,
	'debug' => true,
	'classNormal' => 'btn btn-lg btn-default',
	'classActive' => 'btn btn-lg btn-primary',
	'items' => [
		['label' => Yii::t('companies-question', 'tab_newest'), 'url' => ['companies/question/index', 'company_id' => $company->id]],
		['label' => Yii::t('companies-question', 'tab_popular'), 'url' => ['companies/question/popular', 'company_id' => $company->id]],
		['label' => Yii::t('companies-question', 'tab_discussed'), 'url' => ['companies/question/discussed', 'company_id' => $company->id]],
	]
]) ?>

<div class="clearfix"></div>
