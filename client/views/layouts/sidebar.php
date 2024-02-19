<?php

use yii\helpers\Url;

use yii\helpers\ArrayHelper;

use common\modules\base\extensions\bootstrap\Nav;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;

use common\modules\menu\models\Menu;

?>

<?php

$items = [
	
	// User
	$this->render('_sidebar_user'),
	
	['label' => 'О проекте', 'url' => ['/about'], 'options' => ['class' => 'visible-xs']],
	['label' => 'Сотрудничество', 'url' => ['/cooperation'], 'options' => ['class' => 'visible-xs']],
	['label' => 'Партнеры', 'url' => ['/companies/default/index'], 'visible' => Yii::$app->settings->get('enabled', 'company'), 'options' => ['class' => 'visible-xs']],
	['label' => 'Скидки', 'url' => ['/companies/discount/index'], 'options' => ['class' => 'visible-xs']],
	['label' => 'Контакты', 'url' => ['/rules'], 'options' => ['class' => 'visible-xs']],
	
	// General
	$this->render('_sidebar_heading', ['title' => Yii::t('app', 'menu_general_header')]),
	['label' => Yii::t('dashboard', 'title'), 'icon' => 'fa fa-tachometer', 'url' => ['/dashboard/default/index'], 'visible' => Yii::$app->user->can('dashboard.default.index')],
];

foreach (Menu::find()->where(['visible' => true, 'status' => Status::ENABLED])->all() as $menu) {
	$items[] = [
		'label' => $menu->title,
		'url' => Url::to(['/menus/view', 'id' => $menu->id, 'seo' => true]),
		'icon' => 'fa fa-sitemap',
		'active' => $menu->getUri().'/' == Yii::$app->request->getPathInfo(),
	];
}

$items = ArrayHelper::merge($items, [
	
	// Content
	$this->render('_sidebar_heading', ['title' => Yii::t('app', 'menu_content_header'), 'visible' => (
	true
	)]),
    ['label' => 'Каталог <sup style="color: #FDA088; font-weight: bold">Альфа</sup>', 'icon' => 'fa fa-font', 'url' => 'https://v2.sprut.ai/catalog', 'linkOptions' => ['target' => '_blank']],
	['label' => Yii::t('content-news', 'title'), 'icon' => 'fa fa-newspaper-o', 'url' => ['/news/index']],
	['label' => Yii::t('content-article', 'title'), 'icon' => 'fa fa-shopping-bag', 'url' => ['/article/index']],
	['label' => Yii::t('content-blog', 'title'), 'icon' => 'fa fa-list', 'url' => ['/blog/index'], 'visible' => Yii::$app->settings->get('enabled', 'blog')],
	//['label' => Yii::t('content-shortcut', 'title'), 'icon' => 'fa fa-apple', 'url' => ['/shortcut/index'], 'visible' => Yii::$app->settings->get('enabled', 'shortcut')],
	['label' => Yii::t('project', 'title'), 'icon' => 'fa fa-handshake-o', 'url' => ['/projects/index']],
	['label' => Yii::t('plugin', 'title'), 'icon' => 'fa fa-code', 'url' => ['/plugins/index'], 'visible' => Yii::$app->settings->get('enabled', 'plugin')],
	['label' => Yii::t('content-video', 'title'), 'icon' => 'fa fa-youtube', 'url' => ['/video/index'], 'visible' => Yii::$app->settings->get('enabled', 'video')],
	['label' => Yii::t('tag', 'title'), 'icon' => 'fa fa-tag', 'url' => ['/tags/index']],
	['label' => Yii::t('paste', 'title'), 'icon' => 'fa fa-code-fork', 'url' => ['/pastes/index']],
	//['label' => Yii::t('qa', 'title'), 'icon' => 'fa fa-comments', 'url' => ['/qa/default/index'], 'visible' => !Yii::$app->user->isGuest],
	
	// Company
	$this->render('_sidebar_heading', ['title' => Yii::t('app', 'menu_company_header'), 'visible' => (
	true
	)]),
	['label' => Yii::t('company', 'title_vendor'), 'icon' => 'fa fa-building', 'url' => ['/companies/default/vendors']],
	['label' => Yii::t('company', 'title_integrator'), 'icon' => 'fa fa-wrench', 'url' => ['/companies/default/integrators']],
	['label' => Yii::t('company', 'title_shop'), 'icon' => 'fa fa-shopping-basket', 'url' => ['/companies/default/shops']],
	['label' => Yii::t('portfolio', 'title'), 'icon' => 'fa fa-folder-open', 'url' => ['/portfolio/index']],
	
	// Profile
	$this->render('_sidebar_heading', ['title' => Yii::t('app', 'menu_profile_header'), 'visible' => (
	!Yii::$app->user->isGuest
	)]),
	['label' => Yii::t('favorite', 'title'), 'icon' => 'fa fa-heart', 'url' => ['/favorites/index'], 'visible' => Yii::$app->user->can('favorites.index')],
	['label' => Yii::t('paste', 'title'), 'icon' => 'fa fa-code-fork', 'url' => ['/paste/default/index'], 'visible' => Yii::$app->user->can('paste.default.index')],
	['label' => Yii::t('app', 'menu_profile_data'), 'icon' => 'fa fa-user', 'url' => ['/user/profile/index'], 'visible' => !Yii::$app->user->isGuest],
	['label' => Yii::t('app', 'menu_profile_settings'), 'icon' => 'fa fa-cog', 'url' => ['/user/settings/profile'], 'visible' => !Yii::$app->user->isGuest],
]);

?>

<!-- sidebar-->
<aside class="aside">
	<!-- START Sidebar (left)-->
	<div class="aside-inner">
		<nav data-sidebar-anyclick-close="" class="sidebar">
			<!-- START sidebar nav-->
			<?= Nav::widget([
				'options' => ['class' => 'nav'],
				'activateParents' => true,
				'labelTag' => 'span',
				'iconTag' => 'em',
				'items' => $items,
				'encodeLabels' => false,
			]) ?>
			<!-- END sidebar nav-->
		</nav>
	</div>
	<!-- END Sidebar (left)-->
</aside>