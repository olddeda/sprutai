<?php

use yii\widgets\Breadcrumbs;

use common\modules\base\extensions\pnotify\PNotify;
use common\modules\base\extensions\pnotify\PNotifyAsset;
use common\modules\base\assets\BootboxAsset;

PNotifyAsset::register($this);
BootboxAsset::overrideSystemConfirm();
?>

<!-- Main section-->
<section>
	
	<!-- Page content-->
	<div class="content-wrapper">
		
		<?php if ($this->title) { ?>
		<h1 class="content-heading"><?= $this->title ?></h1>
		<?php } ?>
		
		<?= Breadcrumbs::widget([
			'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		]) ?>
		
		<?php foreach (Yii::$app->session->getAllFlashes() as $type => $text) { ?>
			<?php if (in_array($type, ['success', 'danger', 'warning', 'info'])) { ?>
				<?= PNotify::widget([
					'title' => Yii::t('base', 'notify_title_'.$type),
					'type' => array_search($type, [
						'success' => 'success',
						'error' => 'danger',
						'warning' => 'warning',
						'info' => 'info',
					]),
					'text' => $text,
					'clientOptions' => [
						'styling' => 'bootstrap3',
						'icon' => array_search($type, [
							'fa fa-check' => 'success',
							'fa fa-remove' => 'danger',
							'fa fa-warning' => 'warning',
							'fa fa-info' => 'info',
						]),
						'opacity' => 0.95,
						'shadow' => false,
					],
				]) ?>
			<?php } ?>
		<?php } ?>
		
		<div class="panel panel-default">
			<div class="panel-body">
				<?= $content ?>
			</div>
		</div>
	</div>
	
</section>