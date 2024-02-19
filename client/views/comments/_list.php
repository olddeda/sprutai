<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $comment \common\modules\comments\models\Comment */
/* @var $comments array */
/* @var $maxLevel null|integer comments max level */
?>
<?php if (!empty($comments)) { ?>
<?php foreach ($comments as $comment) { ?>
<li class="comment" id="comment-<?= $comment->id ?>">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="comment-content" data-comment-content-id="<?= $comment->id ?>" data-comment-parent-id="<?= $comment->parent_id ?>">
				<div class="comment-author-avatar">
					<div class="thumbnail">
						<?php $avatar = $comment->getAvatar(50, 50) ?>
						<?php $img = Html::img($avatar, ['class' => 'img-responsive', 'alt' => $comment->getAuthorName()]); ?>
						<?= Html::a($img, $comment->owner->url, ['target' => '_blank', 'data-pjax' => 0]) ?>
					</div>
				</div>
				<div class="comment-details">
					<?php if ($comment->isActive) { ?>
						<div class="comment-action-buttons">
							<?php if (!Yii::$app->user->isGuest && ($comment->level < $maxLevel || is_null($maxLevel))) { ?>
								<?= Html::a("<span class='glyphicon glyphicon-share-alt'></span>", '#', [
									'title' => Yii::t('comments', 'button_reply'),
									'data-toggle' => 'tooltip',
									'class' => 'comment-reply',
									'data' => [
										'action' => 'reply',
										'comment-id' => $comment->id
									],
								]); ?>
							<?php } ?>
							<?php if ($comment->isOwn) { ?>
								<?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', '#', [
									'title' => Yii::t('comments', 'button_update'),
									'data-toggle' => 'tooltip',
									'data' => [
										'action' => 'update',
										'comment-id' => $comment->id,
										'parent-id' => $comment->parent_id,
									],
								]); ?>
								<?= Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
									'title' => Yii::t('comments', 'button_delete'),
									'data-toggle' => 'tooltip',
									'data' => [
										'action' => 'delete',
										'url' => Url::to(['/comments/default/delete', 'id' => $comment->id]),
										'comment-id' => $comment->id,
										'comment-confirm' => Yii::t('comments', 'confirm_delete'),
									]
								]); ?>
							<?php } ?>
                            <div class="clearfix"></div>
							<?= \common\modules\vote\widgets\Vote::widget([
								'viewFile' => '@client/views/vote/vote',
								'entity' => \common\modules\vote\models\Vote::COMMENT_VOTE,
								'model' => $comment,
								'options' => ['class' => 'vote vote-visible-buttons']
							]); ?>
						</div>
					<?php } ?>
					<div class="comment-author-name">
						<span><?= Html::a($comment->owner->title, $comment->owner->url, ['target' => '_blank', 'data-pjax' => 0]); ?></span>
						<span class="comment-date">
							<?php if ($comment->updated_at > $comment->created_at) { ?>
							<?= Yii::t('comments', 'updated_date', ['date' => $comment->getUpdatedDate()]); ?>
							<?php } else { ?>
							<?= $comment->getPostedDate(); ?>
							<?php } ?>
						</span>
					</div>
					<div class="comment-body">
						<?php if ($comment->isActive) { ?>
							<?= $comment->getContent_with_links(); ?>
						<?php } else { ?>
							<i>Комментарий удален</i>
						<?php } ?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<?php if ($comment->hasChildren()) { ?>
		<ul class="children">
			<?= $this->render('_list', [
				'comments' => $comment->children,
				'maxLevel' => $maxLevel
			]) ?>
		</ul>
	<?php } ?>
</li>
<?php } ?>
<?php } ?>