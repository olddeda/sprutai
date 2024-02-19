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
		
		<!-- start header -->
		<?= $this->render('header') ?>
		<!-- end header -->
		
		<!-- start sidebar -->
        <?php if ($this->context->layoutSidebar) { ?>
		<?= $this->render($this->context->layoutSidebar, $this->context->layoutSidebarParams) ?>
        <?php } ?>
		<!-- end sidebar -->
		
		<!-- start offsidebar -->
		<? //= $this->render('offsidebar') ?>
		<!-- end offsidebar -->
		
		<!-- start content -->
		<?= $this->render((isset($this->context->layoutContent) ? $this->context->layoutContent : 'content'), [
			'content' => $content,
		]) ?>
		<!-- end content -->
	
	</div>
	
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
