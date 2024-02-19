<?php
namespace client\controllers;

use Yii;
use yii\httpclient\Client;
use yii\sphinx\Query;
use yii\helpers\Url;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\content\models\Article;

use common\modules\media\helpers\enum\Mode;

/**
 * Demo controller
 */
class DemoController extends Controller
{
	public function behaviors() {
		return [];
	}
	
	public function actionDashboard() {
		return $this->render('dashboard');
	}
	
	public function actionTest() {
		$ids = (new Query())->select('id')->from('idx_content')->match($_GET['search'])->groupBy('id')->column();
		
		/** @var \common\modules\content\models\Article[] $articles */
		$articles = Article::find()->andWhere(['in', 'id', $ids])->all();
		
		if ($articles) {
			foreach ($articles as $article) {
				$url = Url::to(['/article/view', 'id' => $article->id], true);
				
				$item = [
					'id' => $article->id,
					'title' => $article->title,
					'description' => $article->descr,
					'url' => Url::to(['/article/view', 'id' => $article->id], true),
					//'input_message_content' => new InputTextMessageContent(['message_text' => ' ' . $article->descr]),
				];
				
				if ($article->image && $article->image->getFileExists())
					$item['thumb_url'] = str_replace('/client', '', Url::base(true)).$article->image->getImageSrc('100', '100', Mode::CROP_CENTER);
				
				
				Debug::dump($item);
				die;
				
				//$results[] = new InlineQueryResultArticle($item);
			}
			
			
		}
	}
}
