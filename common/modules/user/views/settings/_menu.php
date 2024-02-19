<?php

use yii\widgets\Menu;

/** @var common\modules\user\models\User $user */
$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

?>

<div class="panel panel-default">
    <div class="panel-body">
        <?= Menu::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked',
            ],
            'items' => [
                ['label' => Yii::t('user-profile', 'menu_profile'), 'url' => ['/user/settings/profile']],
                ['label' => Yii::t('user-profile', 'menu_account'), 'url' => ['/user/settings/account']],
	            ['label' => Yii::t('user-profile', 'menu_address'), 'url' => ['/user/address/index']],
                ['label' => Yii::t('user-profile', 'menu_networks'), 'url' => ['/user/settings/networks'], 'visible' => $networksVisible],
				['label' => Yii::t('user-profile', 'menu_subscribe'), 'url' => ['/user/settings/subscribe']],
            ],
        ]) ?>
    </div>
</div>
