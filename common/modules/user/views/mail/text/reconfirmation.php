<?php

/**
 * @var common\modules\user\models\UserToken $token
 */
?>
<?= Yii::t('user-mail', 'Hello') ?>,

<?= Yii::t('user-mail', 'message_we_have_received_a_request_to_change_the_email_address_for_your_account', Yii::$app->name) ?>.
<?= Yii::t('user-mail', 'message_in_order_to_complete_your_registration_please_click_the_link_below') ?>.

<?= $token->url ?>

<?= Yii::t('user-mail', 'message_if_you_cannot_click_the_link_please_try_pasting_the_text_into_your_browser') ?>.

<?= Yii::t('user-mail', 'message_if_you_did_not_make_this_request_you_can_ignore_this_email') ?>.
