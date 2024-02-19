<?php

use yii\bootstrap\Nav;
use yii\web\View;
use yii\helpers\Html;

use common\modules\user\models\User;

/**
 * @var View $this
 * @var User $user
 * @var string $content
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-admin', 'update_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-update">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<?= Nav::widget([
						'options' => [
							'class' => 'nav-pills nav-stacked',
						],
						'items' => [
							[
								'label' => Yii::t('user', 'menu_account_details'),
								'url' => ['/user/admin/update', 'id' => $user->id]
							],
							[
								'label' => Yii::t('user', 'menu_profile_details'),
								'url' => ['/user/admin/update-profile', 'id' => $user->id]
							],
							[
								'label' => Yii::t('user', 'menu_information'),
								'url' => ['/user/admin/info', 'id' => $user->id]
							],
							[
								'label' => Yii::t('user', 'menu_assignments'),
								'url' => ['/user/admin/assignments', 'id' => $user->id],
								'visible' => (isset(Yii::$app->modules['rbac']) && Yii::$app->user->isSuperAdmin),
							],
							'<hr>',
							[
								'label' => Yii::t('user', 'menu_confirm'),
								'url' => ['/user/admin/confirm', 'id' => $user->id],
								'visible' => !$user->isConfirmed,
								'linkOptions' => [
									'class' => 'text-success',
									'data-method' => 'post',
									'data-confirm' => Yii::t('user', 'confirm_activate'),
								],
							],
							[
								'label' => Yii::t('user', 'menu_block'),
								'url' => ['/user/admin/block', 'id' => $user->id],
								'visible' => !$user->isBlocked,
								'linkOptions' => [
									'class' => 'text-danger',
									'data-method' => 'post',
									'data-confirm' => Yii::t('user', 'confirm_block'),
								],
							],
							[
								'label' => Yii::t('user', 'menu_unblock'),
								'url' => ['/user/admin/block', 'id' => $user->id],
								'visible' => $user->isBlocked,
								'linkOptions' => [
									'class' => 'text-success',
									'data-method' => 'post',
									'data-confirm' => Yii::t('user', 'confirm_unblock'),
								],
							],
							[
								'label' => Yii::t('user', 'menu_delete'),
								'url' => ['/user/admin/delete', 'id' => $user->id],
								'linkOptions' => [
									'class' => 'text-danger',
									'data-method' => 'post',
									'data-confirm' => Yii::t('user', 'confirm_delete'),
								],
							],
						],
					]) ?>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-body">
					<?= $content ?>
				</div>
			</div>
		</div>
	</div>

</div>
