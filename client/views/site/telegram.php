<?php

use yii\helpers\Html;
use yii\helpers\Url;



$this->context->layout = 'main_single';
$this->context->layoutContent = 'content_clear';

$this->title = $username;

$this->context->bodyClass = 'maintenance';
?>

<?php

$js = <<<JS
var username = '$username';
var url = '$url';
document.location.href = url;

setTimeout(function() {
	document.close();
}, 1000);

JS;

$this->registerJs($js);
?>

<div class="abs-center margin-top-40">
	<div class="text-center">
		<img src="/client/images/svg/logo.svg" alt="Sprut.ai" class="img-responsive">
	</div>
	<div class="text-center mb-xl">
		<a class="btn btn-default margin-top-20" href="<?= $url ?>">Открыть @<?= $username ?></a>
	</div>
	<div class="p-lg text-center">
		<span>&copy;</span>
		<span>2018 - <?= date('Y') ?></span>
		<span><br/></span>
		<span><?= Yii::$app->name ?></span>
	</div>
</div>