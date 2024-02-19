<?php

/**
 * @var common\modules\user\models\User $user
 * @var common\modules\user\models\UserToken $token
 */
?>
<?= Yii::t('user-mail', 'header_hello') ?>,

<?= Yii::t('user-mail', 'message_we_have_received_a_request_to_reset_the_password_for_your_account_on', Yii::$app->name) ?>.
<?= Yii::t('user-mail', 'message_please_click_the_link_below_to_complete_your_password_reset') ?>.

<?= $token->url ?>

<?= Yii::t('user-mail', 'message_if_you_cannot_click_the_link_please_try_pasting_the_text_into_your_browser') ?>.

<?= Yii::t('user-mail', 'message_if_you_did_not_make_this_request_you_can_ignore_this_email') ?>.
