<?php

/**
 * @var common\modules\user\models\User
 */
?>
<?= Yii::t('user-mail', 'header_hello') ?>,

<?= Yii::t('user', 'message_your_account_has_been_created', Yii::$app->name) ?>.
<?php if ($showPassword || $module->enableGeneratingPassword) { ?>
<?= Yii::t('user-mail', ($module->enableGeneratingPassword ? 'message_we_have_generated_a_password_for_you' : 'message_your_entered_password')) ?>:
<?= $user->password ?>
<?php } ?>

<?php if ($token !== null) { ?>
<?= Yii::t('user', 'message_in_order_to_complete_your_registration_please_click_the_link_below') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'message_if_you_cannot_click_the_link_please_try_pasting_the_text_into_your_browser') ?>.
<?php } ?>

<?= Yii::t('user', 'message_if_you_did_not_make_this_request_you_can_ignore_this_email') ?>.
