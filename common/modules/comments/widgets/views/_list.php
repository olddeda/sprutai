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
			<div class="comment-content" data-comment-content-id="<?= $comment->id ?>" data-comment-parent-id="<?= $comment->parent_id ?>">
                <div class="comment-author-avatar">
					<div class="thumbnail">
						<?php $avatar = $comment->getAvatar(50, 50) ?>
						<?php if ($avatar) { ?>
							<?= Html::img($avatar, ['class' => 'img-responsive', 'alt' => $comment->getAuthorName()]) ?>
						<?php } else { ?>
							<div class="thumbnail-wrapper">
								<div class="glyphicon glyphicon-user"></div>
							</div>
						<?php } ?>
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
									]]); ?>
                            <?php } ?>
							<?php if ($comment->isOwn) { ?>
								<?= Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
									'title' => Yii::t('comments', 'button_delete'),
									'data-toggle' => 'tooltip',
									'data' => [
										'action' => 'delete',
										'url' => Url::to(['/comments/default/delete', 'id' => $comment->id]),
										'comment-id' => $comment->id,
										'comment-confirm' => Yii::t('comments', 'confirm_delete'),
									]]); ?>
							<?php } ?>
                        </div>
                    <?php } ?>
                    <div class="comment-author-name">
                        <span><?= $comment->getAuthorName(); ?></span>
                        <span class="comment-date">
                            <?= $comment->getPostedDate(); ?>
                        </span>
                    </div>
                    <div class="comment-body">
                        <?= $comment->getContent_with_links(); ?>
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
