<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use common\modules\rbac\widgets\Assignments;

/**
 * @var yii\web\View 				$this
 * @var common\modules\user\models\User 	$user
 */

?>

<?php $this->beginContent('@common/modules/user/views/admin/update.php', ['user' => $user]) ?>

<?= Assignments::widget([
	'userId' => $user->id
]) ?>

<?php $this->endContent() ?>
