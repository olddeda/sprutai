<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type;

use common\modules\content\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $project common\modules\project\models\Project */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('project-event', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['/project/default/index']];
$this->params['breadcrumbs'][] = ['label' => $project->title, 'url' => ['/project/default/view', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="project-event-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $project]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => true
			]); ?>
			
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'showFooter' => true,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'columns' => [
	
					// Image
					[
						'attribute' => 'media_image',
						'format' => 'html',
						'label' => '',
						'headerOptions' => ['width' => '90'],
						'value' => function ($data) {
							return ImageShowWidget::widget([
								'model' => $data,
								'width' => 80,
								'height' => 80,
								'mode' => Mode::CROP_CENTER,
							]);
						},
					],
	
					// Id
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '100'],
					],
	
					// Title
					[
						'class' => EditableColumn::class,
						'attribute' => 'title',
						'url' => ['editable', 'project_id' => $project->id],
						'editableOptions' => [
							'title' => Yii::t('content', 'editable_title'),
						],
						'pluginOptions' => [
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'format' => 'raw',
	
					],
	
					// Author
					[
						'label' => $searchModel->getAttributeLabel('author_id'),
						'attribute' => 'author_fio',
						'value' => function($data) {
							return $data->author->fio;
						},
						'visible' => (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor() || Yii::$app->user->getIsCompany()),
					],
	
					// Tags ids
					[
						'attribute' => 'tags_ids',
						'value' => function($data) {
							return $data->getTagsValues(false, '<br/>');
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'tags_ids',
							'items' => Tag::listDataType('id', 'title', 'title', [Type::NONE]),
							'clientOptions' => [
								'allowClear' => true,
								'placeholder' => '',
							],
						]),
						'headerOptions' => ['width' => '300'],
						'format' => 'raw',
					],
	
					// Status
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'attribute' => 'status',
						'value' => function($data) {
							return Status::getLabel($data->status);
						},
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'status',
							'items' => Status::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'url' => ['editable', 'project_id' => $project->id],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('content', 'editable_status'),
								'source' => Status::listData(),
								'hideSearch' => true,
								'value' => $data->status,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 160,
								'hideSearch' => true,
							],
							'disabled' => (!Yii::$app->user->isSuperAdmin && !Yii::$app->user->isAdmin && !Yii::$app->user->isEditor),
						],
						'headerOptions' => ['width' => '150'],
					],
	
					// Date
					[
						'attribute' => 'date_at',
						'value' => function ($data) {
							return $data->getDate();
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'date_at',
							'template' => '{input}{button}{reset}',
							'language' => 'ru',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]),
						'headerOptions' => ['width' => '170'],
					],
                    
                    // Actions
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => [
                            'width' => '75',
                            'style' => 'text-align:center;'
                        ],
                        'buttons' => [
							'view' => function ($url, $model) use ($project) {
								return Yii::$app->user->can('project.event.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', [
									'view',
									'project_id' => $project->id,
									'id' => $model->id,
								], [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
                            'update' => function ($url, $model) use ($project) {
                                return Yii::$app->user->can('project.event.update') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                                    'update',
                                    'project_id' => $project->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_update'),
                                    'data-pjax' => '0',
                                    'data-toggle' => 'tooltip',
                                ]) : '';
                            },
                            'delete' => function ($url, $model) use ($project) {
                                return Yii::$app->user->can('project.event.delete') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                    'delete',
                                    'project_id' => $project->id,
                                    'id' => $model->id,
                                ], [
                                    'title' => Yii::t('base', 'button_delete'),
                                    'data-method' => 'POST',
                                    'data-confirm' => Yii::t('event', 'confirm_delete_name', ['title' => $model->title]),
                                    'data-pjax' => '1',
                                    'data-toggle' => 'tooltip',
                                ]) : '';
                            },
                        ],
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>

		</div>
	</div>

    <div class="margin-20">

        <?php if (Yii::$app->user->can('project.event.create')) { ?>
            <div class="form-group margin-top-20">
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create', 'project_id' => $project->id], [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>
        <?php } ?>

    </div>

</div>
