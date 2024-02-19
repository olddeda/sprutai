<?php

use yii\helpers\Html;

/**
 * @var common\modules\user\Module $module
 * @var common\modules\user\models\User $user
 * @var common\modules\user\models\UserToken $token
 * @var bool $showPassword
 */
?>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user-mail', 'header_hello') ?>,
</p>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user-mail', 'message_your_account_has_been_created', Yii::$app->name) ?>.
</p>

<?php if ($token !== null): ?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user-mail', 'message_in_order_to_complete_your_registration_please_click_the_link_below') ?>.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Html::a(Html::encode($token->url), $token->url); ?>
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user-mail', 'message_if_you_cannot_click_the_link_please_try_pasting_the_text_into_your_browser') ?>.
    <?php endif ?>
</p>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user-mail', 'message_if_you_did_not_make_this_request_you_can_ignore_this_email') ?>.
</p>
