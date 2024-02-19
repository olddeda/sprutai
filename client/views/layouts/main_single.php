<?php

use client\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

	<?= $this->render('head') ?>

    <body class="preload <?= $this->context->bodyClass ?>">

	<?php $this->beginBody() ?>

	<div class="wrapper">
	
		<!-- start content -->
		<?= $this->render($this->context->layoutContent, [
			'content' => $content,
		]) ?>
		<!-- end content -->

	</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
