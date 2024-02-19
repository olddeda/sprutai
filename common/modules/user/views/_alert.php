<div class="row">
	<div class="col-xs-12">
		<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message) { ?>
			<?php if (in_array($type, ['inline-success', 'inline-danger', 'inline-warning', 'inline-info'])) { ?>
				<div class="alert alert-<?= str_replace('inline-', '', $type) ?>">
					<?= $message ?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>