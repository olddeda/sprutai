<?php

/**
 * @var yii\web\View $this
 * @var common\modules\user\models\UserAddress $model
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-address', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ['/user/profile']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('user-address', 'title'), 'url' => ['/user/address/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-md-3">
		<?= $this->render('../settings/_menu') ?>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-body">
				
			    <?= $this->render('_form', [
			        'model' => $model,
                ]); ?>
			
			</div>
		</div>
	</div>
</div>