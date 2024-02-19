<?php
/**
 * @var string $route
 */
use artkost\qa\Module;
?>
<div class="qa-index-header">
    <a class="qa-index-add-button btn btn-primary" href="<?= Module::url(['ask']) ?>"><?= Yii::t('qa','button_question_create') ?></a>
    <ul class="qa-index-tabs nav nav-tabs">
        <li <?= ($route == 'index') ? 'class="active"':''?>><a href="<?= Module::url(['index']) ?>"><?= Yii::t('qa','tab_new') ?></a></li>
		<li <?= ($route == 'popular') ? 'class="active"':''?>><a href="<?= Module::url(['popular']) ?>"><?= Yii::t('qa','tab_active') ?></a></li>
		<li <?= ($route == 'my') ? 'class="active"':''?>><a href="<?= Module::url(['my']) ?>"><?= Yii::t('qa','tab_my') ?></a></li>
		<li <?= ($route == 'favorite') ? 'class="active"':''?>><a href="<?= Module::url(['favorite']) ?>"><?= Yii::t('qa','tab_favorite') ?></a></li>
		<li <?= ($route == 'closed') ? 'class="active"':''?>><a href="<?= Module::url(['closed']) ?>"><?= Yii::t('qa','tab_closed') ?></a></li>
    </ul>
</div>