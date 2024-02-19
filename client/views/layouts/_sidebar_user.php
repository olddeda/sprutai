<?php

use yii\helpers\Url;


$isGuest = Yii::$app->user->isGuest;

$imageSrc = (!$isGuest) ? Yii::$app->user->identity->avatar->getImageSrc(80, 80) : false;
if (!$imageSrc)
	$imageSrc = Yii::$app->request->baseUrl.'/images/svg/cover_avatar.svg';

$url = ($isGuest) ? Url::to('/client/user/signin') : Url::to(['/user/profile/index']);
$username = (Yii::$app->user->isGuest) ? 'Гость' : Yii::$app->user->identity->profile->first_name ?: Yii::$app->user->identity->username;

?>

<li class="has-user-block">
	<div id="user-block">
		<div class="item user-block">
			<!-- User picture-->
			<div class="user-block-picture">
				<div class="user-block-status">
					<a href="<?= $url ?>">
						<img src="<?= $imageSrc ?>" width="80" height="80" class="img-thumbnail img-circle">
					</a>
					<!--<div class="circle circle-primary circle-lg"></div>-->
				</div>
			</div>
			<!-- Name and Job-->
			<div class="user-block-info">
				<a href="<?= $url ?>">
					<span class="user-block-name">Привет, <?= $username ?></span>
					<?php if (!$isGuest) { ?>
					<span class="user-block-role"><?= Yii::$app->user->role->description ?></span>
					<?php } ?>
				</a>
				<?php if (!$isGuest && Yii::$app->session->get('admin_id')) { ?>
				<a href="<?= Url::to(['/user/admin/logout']) ?>">Вернуться в аккаунт</a>
				<?php } ?>
			</div>
		</div>
	</div>
</li>