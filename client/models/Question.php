<?php
namespace client\models;

use Yii;

use artkost\qa\models\Question as BaseQuestion;

class Question extends BaseQuestion
{
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['title', 'content', 'tags'], 'required'],
			[['tags'], 'normalizeTags'],
			[['title', 'content', 'tags'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
		];
	}
	
	
	public function isAuthor() {
		if (Yii::$app->user->isAdmin)
			return true;
		return parent::isAuthor();
	}
}