<?php
$content = \common\modules\content\models\Page::findBySlug('content-social');
?>

<?php if ($content) { ?>
<hr>
<div class="content-social">
	<?= $content->text ?>
</div>
<?php } ?>