<?php
/**
 * @var \artkost\qa\models\Answer $model
 * @var \artkost\qa\models\Question $question
 */

use artkost\qa\Module;

$this->title = Yii::t('qa', 'title_answer_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('qa', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $question->title, 'url' => ['view', 'id' => $question->id, 'alias' => $question->alias]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-lg-12">
        <div class="qa-view-answer-form">
            <?= $this->render('parts/form-answer', [
            	'model' => $model,
				'action' => Module::url(['answer', 'id' => $model->id])
			]); ?>
        </div>
    </div>
</div>

