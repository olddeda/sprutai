<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yii\web\View
 * @var common\modules\user\models\User
 */
?>

<?php $this->beginContent('@common/modules/user/views/admin/update.php', ['user' => $user]) ?>

<table class="table">
    <tr>
        <td><strong><?= Yii::t('user', 'field_created_at') ?>:</strong></td>
        <td><?= Yii::t('user', 'format_created_at', [$user->created_at]) ?></td>
    </tr>
    <?php if ($user->registration_ip !== null): ?>
        <tr>
            <td><strong><?= Yii::t('user', 'field_registration_ip') ?>:</strong></td>
            <td><?= $user->registration_ip ?></td>
        </tr>
    <?php endif ?>
    <tr>
        <td><strong><?= Yii::t('user', 'header_confirmation') ?>:</strong></td>
        <?php if ($user->isConfirmed): ?>
            <td class="text-success"><?= Yii::t('user', 'format_confirmed_at', [$user->confirmed_at]) ?></td>
        <?php else: ?>
            <td class="text-danger"><?= Yii::t('user', 'status_unconfirmed') ?></td>
        <?php endif ?>
    </tr>
    <tr>
        <td><strong><?= Yii::t('user', 'header_block') ?>:</strong></td>
        <?php if ($user->isBlocked): ?>
            <td class="text-danger"><?= Yii::t('user', 'format_blocked_at', [$user->blocked_at]) ?></td>
        <?php else: ?>
            <td class="text-success"><?= Yii::t('user', 'status_no_blocked') ?></td>
        <?php endif ?>
    </tr>
</table>

<?php $this->endContent() ?>
