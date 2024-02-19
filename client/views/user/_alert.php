<?php

use yii\helpers\Html;

?>

<div class="user-signup block-center mt-xl wd-xxl">
	<!-- START panel-->
	<div class="panel panel-primary panel-flat">
		<div class="panel-heading text-center">
			<?= Html::a(Html::img('@web/images/svg/logo.svg', [
				'class' => 'block-center img-rounded',
			])) ?>
		</div>
		<div class="panel-body">
			<h1 class="text-center pv"><?= $this->title ?></h1>
			
			<div class="row">
				<div class="col-xs-12">
					<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message) { ?>
						<?php if (in_array($type, ['inline-success', 'inline-danger', 'inline-warning', 'inline-info'])) { ?>
							<div class="alert">
								<?= $message ?>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<!-- END panel-->

	<div class="p-lg text-center">
		<span>&copy;</span>
		<span><?= date('Y') ?></span>
		<span><br/></span>
		<span><?= Yii::$app->name ?></span>
	</div>
</div>