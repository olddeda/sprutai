<?php
namespace common\modules\content\components;

use common\modules\content\models\Content;
use Yii;
use yii\base\Component;

use common\modules\base\components\Debug;

use common\modules\content\models\ContentUnique;
use common\modules\content\helpers\enum\StatusUnique;
use yii\helpers\Url;

/**
 * Class Unique
 * @package common\modules\content\components
 */
class Unique extends Component
{
	/**
	 * @var string
	 */
	public $service = 'textru';
	
	/**
	 * @param int $contentId
	 * @param string $text
	 *
	 * @return bool
	 * @throws \yii\base\InvalidConfigException
	 */
	public function check(int $contentId, string $text) {
		$content = Content::find()->where(['id' => $contentId])->one();
		$contentUrl = Url::to($content->url, true);
		
		$model = ContentUnique::find()->where([
			'content_id' => $contentId,
			'text' => $text,
		])->one();
		if (is_null($model)) {
			$result = $this->_getService()->queue($text, [$contentUrl]);
			
			if ($result->error->code) {
				
				if ($result->error->code == 142) {
					$this->_sendError('Text.RU: No balance');
				}
				
				return false;
			}
			
			$model = new ContentUnique();
			$model->content_id = $contentId;
			$model->text = $text;
			$model->uid = $result->text_uid;
			$model->status = StatusUnique::QUEUE;
			$model->save();
		}
		
		if ($model->status == StatusUnique::QUEUE) {
			$result = $this->_getService()->result($model->uid);
			
			$needSave = false;
			
			if ($result->error->code) {
				$model->queue = $result->queuetext;
				$needSave = true;
			}
			
			if ($result->data) {
				$model->unique = $result->data->unique;
				$model->urls = $result->data->urls;
				
				if ($model->urls) {
					foreach ($model->urls as $u) {
						if ($contentUrl == $u->url) {
							$model->unique = (double)$u->plagiat;
							break;
						}
					}
				}
				
				$needSave = true;
			}
			
			if ($result->seo) {
				$model->count_chars_with_space = $result->seo->count_chars_with_space;
				$model->count_chars_without_space = $result->seo->count_chars_without_space;
				$model->count_words = $result->seo->count_words;
				$model->water_percent = $result->seo->water_percent;
				$model->spam_percent = $result->seo->spam_percent;
				$needSave = true;
			}
			
			if ($result->spellcheck) {
				$model->spellcheck = $result->spellcheck;
				$needSave = true;
			}
			
			if ($result->data && $result->seo && $result->spellcheck) {
				$model->status = StatusUnique::COMPLETE;
				$needSave = true;
			}
			
			if ($needSave)
				$model->save();
		}
	
		return $model;
	}
	
	/**
	 * @return object|null
	 * @throws \yii\base\InvalidConfigException
	 */
	private function _getService() {
		return Yii::$app->get($this->service);
	}
	
	private function _sendError($message) {
		
		/** @var \common\modules\base\extensions\telegram\Telegram $telegram */
		$telegram = Yii::$app->telegram;
		
		$telegram->sendMessage([
			'chat_id' => 357615556,
			'text' => $message,
		]);
	}
}