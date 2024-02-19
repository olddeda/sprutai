<?php

use yii\helpers\Html;
use yii\helpers\Url;

use appmake\yii2\bootstrap\Nav;


$hiddenNavbar = (!Yii::$app->user->getIsAdmin() && !Yii::$app->user->getIsEditor()) ? 'margin: 0 !important' : '';
?>

<!-- top navbar-->
<header class="topnavbar-wrapper">
	<!-- START Top Navbar-->
	<nav role="navigation" class="navbar topnavbar">
		
		<!-- START navbar header-->
		<div class="navbar-header">
			<a href="<?= Url::home() ?>" class="navbar-brand">
				<div class="brand-logo">
					<img src="<?= Yii::$app->request->baseUrl ?>/images/svg/logo_text.svg" alt="<?= Yii::$app->name ?>" class="img-responsive">
				</div>
				<div class="brand-logo-collapsed">
					<img src="<?= Yii::$app->request->baseUrl ?>/images/svg/logo.svg" alt="<?= Yii::$app->name ?>" class="img-responsive">
				</div>
			</a>
		</div>
		<!-- END navbar header-->
		
		<!-- START Nav wrapper-->
		<div class="nav-wrapper">

			<!-- START Left navbar-->
			<ul class="nav navbar-nav" style="<?= $hiddenNavbar ?>">
				<li>
					<a href="#" data-trigger-resize="" data-toggle-state="aside-collapsed" class="hidden-xs">
						<em class="fa fa-navicon"></em>
					</a>
					<a href="#" data-search-open="" class="visible-xs sidebar-toggle" style="right: 76px;top: 6px;">
						<em class="icon-magnifier"></em>
					</a>
					<?php if (Yii::$app->user->isGuest) { ?>
					<?= Html::a(Html::tag('em', '', ['class' => 'icon-key']), ['/user/signin'], ['class' => 'visible-xs sidebar-toggle', 'style' => 'right: 40px; top: 6px']) ?>
					<?php } else { ?>
					<?= Html::a(Html::tag('em', '', ['class' => 'icon-notebook']), ['/user/logout'], ['data-method' => 'post', 'class' => 'visible-xs sidebar-toggle', 'style' => 'right: 40px; top: 6px']) ?>
					<?php } ?>
					<a href="#" data-toggle-state="aside-toggled" data-no-persist="true" class="visible-xs sidebar-toggle">
						<em class="fa fa-navicon"></em>
					</a>
				</li>
			</ul>
			
			<?php  ?>
				<?= Nav::widget([
					'options' => ['class' => 'navbar-nav navbar-left', 'style' => $hiddenNavbar],
					'activateParents' => true,
					'items' => [
						['label' => 'О проекте', 'url' => ['/about'], 'options' => ['class' => 'hidden-xs']],
						['label' => 'Сотрудничество', 'url' => ['/cooperation'], 'options' => ['class' => 'hidden-xs']],
						['label' => 'Партнеры', 'url' => ['/companies/default/index'], 'visible' => Yii::$app->settings->get('enabled', 'company'), 'options' => ['class' => 'hidden-xs']],
						['label' => 'Скидки', 'url' => ['/companies/discount/index'], 'options' => ['class' => 'hidden-xs']],
						['label' => 'Контакты', 'url' => ['/rules'], 'options' => ['class' => 'hidden-xs']],
						['label' => '|', 'visible' => Yii::$app->user->can('content.page.index'), 'options' => ['class' => 'hidden-xs']],
						['label' => Yii::t('app', 'navbar_content'), 'icon' => 'fa fa-file-text', 'items' => [
							['label' => Yii::t('content-page', 'title'), 'url' => ['/content/page/index'], 'visible' => Yii::$app->user->can('content.page.index')],
							['label' => Yii::t('content-article', 'title'), 'url' => ['/content/article/index'], 'visible' => Yii::$app->user->can('content.article.index')],
							['label' => Yii::t('content-news', 'title'), 'url' => ['/content/news/index'], 'visible' => Yii::$app->user->can('content.news.index')],
							//['label' => Yii::t('content-shortcut', 'title'), 'url' => ['/content/shortcut/index'], 'visible' => Yii::$app->user->can('content.shortcut.index')],
							['label' => Yii::t('project', 'title'), 'url' => ['/project/default/index'], 'visible' => Yii::$app->user->can('project.default.index')],
							['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index'], 'visible' => Yii::$app->user->can('plugin.default.index')],
							['label' => Yii::t('content-blog', 'title'), 'url' => ['/content/blog/index'], 'visible' => (Yii::$app->user->can('content.blog.index') && Yii::$app->settings->get('enabled', 'blog'))],
							['label' => Yii::t('comments', 'title'), 'url' => ['/comments/manage/index'], 'visible' => Yii::$app->user->can('comments.manage.index')],
							['label' => Yii::t('menu', 'title'), 'url' => ['/menu/default/index'], 'visible' => Yii::$app->user->can('menu.default.index')],
							['label' => Yii::t('banner', 'title'), 'url' => ['/banner/default/index'], 'visible' => Yii::$app->user->can('banner.default.index')],
							['label' => Yii::t('contest', 'title'), 'url' => ['/contest/default/index'], 'visible' => Yii::$app->user->can('contest.default.index')],
						], 'visible' => (
							Yii::$app->user->can('content.page.index') ||
							Yii::$app->user->can('content.article.index') ||
							Yii::$app->user->can('content.news.index') ||
							Yii::$app->user->can('project.default.index') ||
							Yii::$app->user->can('plugin.default.index') ||
							Yii::$app->user->can('blog.default.index') ||
							Yii::$app->user->can('comments.article.index')
						)],
						['label' => Yii::t('app', 'navbar_catalog'), 'icon' => 'fa fa-sitemap', 'items' => [
							['label' => Yii::t('item', 'title'), 'url' => ['/item/default/index'], 'visible' => Yii::$app->user->can('item.default.index')],
						], 'visible' => (
							Yii::$app->user->can('item.default.index')
						)],
						['label' => Yii::t('company', 'title'), 'icon' => 'fa fa-building', 'items' => [
							['label' => Yii::t('company', 'title_index'), 'url' => ['/company/default/index'], 'visible' => Yii::$app->user->can('company.default.index')],
							['label' => Yii::t('company-portfolio', 'title'), 'url' => ['/company/default/portfolio'], 'visible' => Yii::$app->user->can('company.default.portfolio')],
						], 'visible' => (
							Yii::$app->user->can('company.default.index')
						)],
						['label' => Yii::t('app', 'navbar_finance'), 'icon' => 'fa fa-credit-card', 'items' => [
							['label' => Yii::t('app', 'navbar_finance_payment'), 'items' => [
								['label' => Yii::t('payment', 'title'), 'url' => ['/payment/default/index'], 'visible' => Yii::$app->user->can('payment.default.index')],
								['label' => Yii::t('payment-withdrawal', 'title'), 'url' => ['/payment/withdrawal/index'], 'visible' => Yii::$app->user->can('payment.withdrawal.index')],
								['label' => Yii::t('payment-type', 'title'), 'url' => ['/payment/type/index'], 'visible' => Yii::$app->user->can('payment.type.index')],
							], 'visible' => (
								Yii::$app->user->can('payment.default.index') ||
								Yii::$app->user->can('payment.withdrawal.index') ||
								Yii::$app->user->can('payment.type.index')
							)],
						], 'visible' => (
							Yii::$app->user->can('payment.default.index') ||
							Yii::$app->user->can('payment.type.index')
						)],
						['label' => Yii::t('app', 'navbar_users'), 'icon' => 'fa fa-users', 'items' => [
							['label' => Yii::t('app', 'navbar_users_manage'), 'url' => ['/user/admin/index'], 'visible' => Yii::$app->user->can('user.admin.index')],
							['label' => Yii::t('app', 'navbar_users_rbac'), 'items' => [
								['label' => Yii::t('app', 'navbar_users_rbac_role'), 'url' => ['/rbac/role/index', 'visible' => Yii::$app->user->can('rbac.role.index')]],
								['label' => Yii::t('app', 'navbar_users_rbac_task'), 'url' => ['/rbac/task/index', 'visible' => Yii::$app->user->can('rbac.task.index')]],
								['label' => Yii::t('app', 'navbar_users_rbac_permission'), 'url' => ['/rbac/permission/index', 'visible' => Yii::$app->user->can('rbac.permission.index')]],
							], 'visible' => (
								Yii::$app->user->can('rbac.role.index') ||
								Yii::$app->user->can('rbac.task.index') ||
								Yii::$app->user->can('rbac.permission.index')
							)],
						], 'visible' => (
							Yii::$app->user->can('user.admin.index') ||
							Yii::$app->user->can('rbac.role.index') ||
							Yii::$app->user->can('rbac.task.index') ||
							Yii::$app->user->can('rbac.permission.index')
						)],
						['label' => Yii::t('app', 'navbar_tools'), 'icon' => 'glyphicon glyphicon-wrench', 'items' => [
							['label' => Yii::t('notification', 'title'), 'items' => [
								['label' => Yii::t('notification', 'title_send'), 'url' => ['/notification/default/send'], 'visible' => Yii::$app->user->can('notification.default.send')],
							], 'visible' => (
								Yii::$app->user->can('notification.default.send')
							)],
							['label' => Yii::t('telegram', 'title'), 'items' => [
								['label' => Yii::t('telegram-chat', 'title'), 'url' => ['/telegram/chat/index'], 'visible' => Yii::$app->user->can('telegram.chat.index')],
							], 'visible' => (
							Yii::$app->user->can('telegram.chat.index')
							)],
							['label' => Yii::t('app', 'navbar_tools_media'), 'items' => [
								['label' => Yii::t('app', 'navbar_tools_media_default'), 'url' => ['/media/default/index'], 'visible' => Yii::$app->user->can('media.default.index')],
								['label' => Yii::t('app', 'navbar_tools_media_images'), 'url' => ['/media/image/index'], 'visible' => Yii::$app->user->can('media.image.index')],
								['label' => Yii::t('app', 'navbar_tools_media_formats'), 'url' => ['/media/format/index'], 'visible' => Yii::$app->user->can('media.format.index')],
							], 'visible' => (
								Yii::$app->user->can('media.default.index') ||
								Yii::$app->user->can('media.format.index')
							)],
							['label' => Yii::t('app', 'navbar_tools_lookup'), 'url' => ['/lookup/default/index'], 'visible' => Yii::$app->user->can('lookup.default.index')],
							['label' => Yii::t('app', 'navbar_tools_tag'), 'url' => ['/tag/default/index'], 'visible' => Yii::$app->user->can('tag.default.index')],
                            ['label' => Yii::t('app', 'navbar_tools_shortener'), 'url' => ['/shortener/default/index'], 'visible' => Yii::$app->user->can('shortener.default.index')],
							['label' => Yii::t('app', 'navbar_tools_audit'), 'items' => [
								['label' => Yii::t('app', 'navbar_tools_audit_statistics'), 'url' => ['/audit/default/index'], 'visible' => Yii::$app->user->can('audit.default.index')],
								['label' => Yii::t('app', 'navbar_tools_audit_entry'), 'url' => ['/audit/entry/index'], 'visible' => Yii::$app->user->can('audit.entry.index')],
								['label' => Yii::t('app', 'navbar_tools_audit_trail'), 'url' => ['/audit/trail/index'], 'visible' => Yii::$app->user->can('audit.trail.index')],
								['label' => Yii::t('app', 'navbar_tools_audit_error'), 'url' => ['/audit/error/index'], 'visible' => Yii::$app->user->can('audit.error.index')],
								['label' => Yii::t('app', 'navbar_tools_audit_javascript'), 'url' => ['/audit/javascript/index'], 'visible' => Yii::$app->user->can('audit.javascript.index')],
								['label' => Yii::t('app', 'navbar_tools_audit_mail'), 'url' => ['/audit/mail/index'], 'visible' => Yii::$app->user->can('audit.mail.index')],
							], 'visible' => (
								Yii::$app->user->can('audit.default.index') ||
								Yii::$app->user->can('audit.entry.index') ||
								Yii::$app->user->can('audit.trail.index') ||
								Yii::$app->user->can('audit.error.index') ||
								Yii::$app->user->can('audit.javascript.index') ||
								Yii::$app->user->can('audit.mail.index')
							)],
							['label' => Yii::t('app', 'navbar_tools_seo'), 'items' => [
								['label' => Yii::t('app', 'navbar_tools_seo_default'), 'url' => ['/seo/default/index'], 'visible' => Yii::$app->user->can('seo.default.index')],
								['label' => Yii::t('app', 'navbar_tools_seo_modules'), 'url' => ['/seo/module/index'], 'visible' => Yii::$app->user->can('seo.module.index')],
							], 'visible' => (
								Yii::$app->user->can('seo.default.index') ||
								Yii::$app->user->can('seo.module.index')
							)],
							['label' => Yii::t('app', 'navbar_tools_queues'), 'items' => [
								['label' => Yii::t('queues', 'title'), 'url' => ['/queues/default/index'], 'visible' => Yii::$app->user->can('queues.default.index')],
								['label' => Yii::t('queues-job', 'title'), 'url' => ['/queues/job/index'], 'visible' => Yii::$app->user->can('queues.job.index')],
								['label' => Yii::t('queues-worker', 'title'), 'url' => ['/queues/worker/index'], 'visible' => Yii::$app->user->can('queues.worker.index')],
							], 'visible' => (
								Yii::$app->user->can('queues.default.index') ||
								Yii::$app->user->can('queues.job.index') ||
								Yii::$app->user->can('queues.worker.index')
							)],
							['label' => Yii::t('app', 'navbar_tools_asynctask'), 'items' => [
								['label' => Yii::t('app', 'navbar_tools_asynctask_default'), 'url' => ['/asynctask/default/index'], 'visible' => Yii::$app->user->can('asynctask.default.index')],
								['label' => Yii::t('app', 'navbar_tools_asynctask_queue'), 'url' => ['/asynctask/queue/index'], 'visible' => Yii::$app->user->can('asynctask.queue.index')],
								['label' => Yii::t('app', 'navbar_tools_asynctask_worker'), 'url' => ['/asynctask/worker/index'], 'visible' => Yii::$app->user->can('asynctask.worker.index')],
								['label' => Yii::t('app', 'navbar_tools_asynctask_schedule'), 'url' => ['/asynctask/schedule/index'], 'visible' => Yii::$app->user->can('asynctask.schedule.index')],
								['label' => Yii::t('app', 'navbar_tools_asynctask_retry'), 'url' => ['/asynctask/retry/index'], 'visible' => Yii::$app->user->can('asynctask.retry.index')],
								
							], 'visible' => (
								Yii::$app->user->can('asynctask.default.index') ||
								Yii::$app->user->can('asynctask.queue.index') ||
								Yii::$app->user->can('asynctask.retry.index') ||
								Yii::$app->user->can('asynctask.schedule.index') ||
								Yii::$app->user->can('asynctask.worker.index')
							)],
							['label' => Yii::t('app', 'navbar_tools_settings'), 'url' => ['/settings/default/index'], 'visible' => Yii::$app->user->can('settings.default.index')],
							['label' => Yii::t('app', 'navbar_tools_caches'), 'url' => ['/caches/default/index'], 'visible' => Yii::$app->user->can('caches.default.index')],
						], 'visible' => (
							Yii::$app->user->can('media.default.index') ||
							Yii::$app->user->can('tag.default.index') ||
							Yii::$app->user->can('lookup.default.index') ||
							Yii::$app->user->can('audit.default.index') ||
							Yii::$app->user->can('audit.entry.index') ||
							Yii::$app->user->can('audit.trail.index') ||
							Yii::$app->user->can('audit.error.index') ||
							Yii::$app->user->can('seo.default.index') ||
							Yii::$app->user->can('seo.module.index') ||
							Yii::$app->user->can('queues.default.index') ||
							Yii::$app->user->can('queues.job.index') ||
							Yii::$app->user->can('queues.worker.index') ||
							Yii::$app->user->can('settings.default.index') ||
							Yii::$app->user->can('notification.default.send')
						)],
					],
				]);?>
			<?php  ?>
			
			<!-- END Left navbar-->
			<!-- START Right Navbar-->
			<ul class="nav navbar-nav navbar-right hidden-xs">
                <!-- Search icon-->
                <li>
                    <a href="#" data-search-open="">
                        <em class="icon-magnifier"></em>
                    </a>
                </li>
				<?php if (Yii::$app->user->isGuest) { ?>
					<li>
						<?= Html::a(Html::tag('em', '', ['class' => 'icon-key']), ['/user/signin']) ?>
					</li>
				<? }  else { ?>
				<li>
					<?= Html::a(Html::tag('em', '', ['class' => 'icon-notebook']), ['/user/logout'], ['data-method' => 'post']) ?>
				</li>
				<?php } ?>
				<? /*
				<!-- START Alert menu-->
				<li class="dropdown dropdown-list">
					<a href="#" data-toggle="dropdown">
						<em class="icon-bell"></em>
						<div class="label label-danger">11</div>
					</a>
					<!-- START Dropdown menu-->
					<ul class="dropdown-menu animated flipInX">
						<li>
							<!-- START list group-->
							<div class="list-group">
								<!-- list item-->
								<a href="#" class="list-group-item">
									<div class="media-box">
										<div class="pull-left">
											<em class="fa fa-twitter fa-2x text-info"></em>
										</div>
										<div class="media-box-body clearfix">
											<p class="m0">New followers</p>
											<p class="m0 text-muted">
												<small>1 new follower</small>
											</p>
										</div>
									</div>
								</a>
								<!-- list item-->
								<a href="#" class="list-group-item">
									<div class="media-box">
										<div class="pull-left">
											<em class="fa fa-envelope fa-2x text-warning"></em>
										</div>
										<div class="media-box-body clearfix">
											<p class="m0">New e-mails</p>
											<p class="m0 text-muted">
												<small>You have 10 new emails</small>
											</p>
										</div>
									</div>
								</a>
								<!-- list item-->
								<a href="#" class="list-group-item">
									<div class="media-box">
										<div class="pull-left">
											<em class="fa fa-tasks fa-2x text-success"></em>
										</div>
										<div class="media-box-body clearfix">
											<p class="m0">Pending Tasks</p>
											<p class="m0 text-muted">
												<small>11 pending task</small>
											</p>
										</div>
									</div>
								</a>
								<!-- last list item-->
								<a href="#" class="list-group-item">
									<small>More notifications</small>
									<span class="label label-danger pull-right">14</span>
								</a>
							</div>
							<!-- END list group-->
						</li>
					</ul>
					<!-- END Dropdown menu-->
				</li>
				<!-- END Alert menu-->
				<!-- START Offsidebar button-->
				<li>
					<a href="#" data-toggle-state="offsidebar-open" data-no-persist="true">
						<em class="icon-notebook"></em>
					</a>
				</li>
 				*/ ?>
				<!-- END Offsidebar menu-->
			</ul>
			<!-- END Right Navbar-->
		</div>
		<!-- END Nav wrapper-->
		<!-- START Search form-->
		<form role="search" action="/client/search/index" method="get" class="navbar-form">
			<div class="form-group has-feedback">
				<input type="text" name="query" placeholder="<?= Yii::t('search', 'placeholder_search') ?>" class="form-control">
				<div data-search-dismiss="" class="fa fa-times form-control-feedback"></div>
			</div>
			<button type="submit" class="hidden btn btn-default">Submit</button>
		</form>
		<!-- END Search form-->
	</nav>
	<!-- END Top Navbar-->
</header>
