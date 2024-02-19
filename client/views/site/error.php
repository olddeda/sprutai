<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->context->layout = 'main_single';
$this->context->layoutContent = 'content_clear';

$this->title = $exception->statusCode;
?>

<div class="abs-center wd-xxl">
	<!-- START panel-->
	<div class="text-center mb-xl">
		<div class="text-lg mb-lg"><?= $this->title ?></div>
		<p class="lead m0"><?= nl2br(Html::encode($message)) ?></p>
	</div>
	<form action="/client/search/index">
	<div class="input-group mb-xl">
		<input type="text" name="query" placeholder="<?= Yii::t('error', 'placeholder_search') ?>" class="form-control">
		<span class="input-group-btn">
            <button type="submit" class="btn btn-default">
               <em class="fa fa-search"></em>
            </button>
        </span>
	</div>
	</form>
	<ul class="list-inline text-center text-sm mb-xl">
		<li><?= Html::a(Yii::t('error', 'link_main'), ['/'], ['class' => 'text-muted']) ?></li>
		<?php if (Yii::$app->user->isGuest) { ?>
		<li class="text-muted">|</li>
		<li><?= Html::a(Yii::t('error', 'link_signin'), ['/user/signin'], ['class' => 'text-muted']) ?></li>
		<li class="text-muted">|</li>
			<li><?= Html::a(Yii::t('error', 'link_signup'), ['/user/signup'], ['class' => 'text-muted']) ?></li>
		<?php } ?>
	</ul>
	<div class="p-lg text-center">
		<span>&copy;</span>
		<span><?= date('Y') ?></span>
		<span><br/></span>
		<span><?= Yii::$app->name ?></span>
	</div>
</div>