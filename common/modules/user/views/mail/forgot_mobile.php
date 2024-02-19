<?php

use yii\helpers\Html;

/**
 * @var common\modules\user\models\User $user
 * @var common\modules\user\models\UserToken $token
 */
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	<?= Yii::t('user-mail', 'header_hello') ?>,
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	<?= Yii::t('user-mail', 'message_we_have_received_a_request_to_reset_the_password_for_your_account_on', Yii::$app->name) ?>.
	<?= Yii::t('user-mail', 'message_please_user_the_code_below_to_complete_your_password_reset') ?>.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	<?= Yii::t('user-mail', 'message_your_code_password_reset') ?>: <b><?= $token->code ?></b>
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
	<?= Yii::t('user-mail', 'message_if_you_did_not_make_this_request_you_can_ignore_this_email') ?>.
</p>