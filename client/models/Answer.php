<?php
namespace client\models;

use Yii;

use artkost\qa\models\Answer as BaseAnswer;

class Answer extends BaseAnswer
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['content'], 'required'],
			[['question_id'], 'exist', 'targetClass' => Question::className(), 'targetAttribute' => 'id'],
			[['content'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
		];
	}
	
	public function isAuthor() {
		if (Yii::$app->user->isAdmin)
			return true;
		return parent::isAuthor();
	}
}