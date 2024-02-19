<?php

use yii\helpers\Html;

use common\modules\base\components\Debug;

use common\modules\user\widgets\Connect;

/*
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-profile', 'title_networks');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ['/user/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-settings-networks">
	<div class="row">
		<div class="col-md-3">
			<?= $this->render('_menu') ?>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-body">
					<?php $auth = Connect::begin([
						'baseAuthUrl' => ['/user/security/auth'],
						'accounts' => $user->accounts,
						'autoRender' => false,
						'popupMode' => false,
					]) ?>
					<table class="table">
						<?php foreach ($auth->getClients() as $client) { ?>
							<?php $account = $auth->getAccount($client) ?>
							<?php if ($client->getId() == 'telegram' || strpos($client->getId(), 'API') !== false) continue; ?>
							<tr>
								<td style="width: 32px; text-align: center; vertical-align: middle;">
									<?= Html::tag('span', '', [
										'class' => 'fa fa-'.$client->getIcon(),
										'style' => 'font-size:30px',
									]) ?>
								</td>
								<td style="vertical-align: middle">
									<strong><?= $client->getTitle() ?></strong>
									<?php if ($account && !$account->client_id && $client->getId() == 'telegram') { ?>
									<p><?= Yii::t('user-profile', 'message_telegram_connect_instructions', ['code' => $account->code]) ?></p>
									<?php } ?>
								</td>
								<td style="width: 120px;vertical-align: top;">
									<?php if ($account) { ?>
										<?php if ($account->client_id) { ?>
											<?= Html::a(Yii::t('user-profile', 'button_disconnect'), $auth->createClientUrl($client), [
												'class' => 'btn btn-danger btn-block',
												'data-method' => 'post',
											]); ?>
										<?php } else if ($client->getId() == 'telegram') { ?>
											<?= Html::a(Yii::t('user-profile', 'button_connect_cancel'), $auth->createClientUrl($client), [
												'class' => 'btn btn-default btn-block',
												'data-method' => 'post',
											]); ?>
										<?php } else { ?>
											<?= Html::a(Yii::t('user-profile', 'button_connect'), $auth->createClientUrl($client), [
												'class' => 'btn btn-primary btn-block',
											]); ?>
										<?php } ?>
									<?php } else { ?>
										<?= Html::a(Yii::t('user-profile', 'button_connect'), $auth->createClientUrl($client), [
											'class' => 'btn btn-primary btn-block',
										]); ?>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</table>
					<?php Connect::end() ?>
				</div>
			</div>
		</div>
	</div>
</div>
