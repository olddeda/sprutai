<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use yii\helpers\Url;

use common\modules\base\extensions\rss\RssView;
use common\modules\base\extensions\rss\Feed;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\content\helpers\enum\Type;
use common\modules\content\models\Content;

class SiteController extends Controller
{
	/**
	 * @return array
	 */
	public function actions_() {
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}
	
	/**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
    	//return $this->render('index');
        return $this->redirect('/client');
    }
    
    public function actionRss() {
	
	    $query = Content::find()->where([
	    	'status' => Status::ENABLED
		])->andWhere(['in', 'type', [
			Type::ARTICLE,
			Type::NEWS
		]])->orderBy('date_at DESC');
    	
	    $dataProvider = new ActiveDataProvider([
		    'query' => $query,
		    'pagination' => [
			    'pageSize' => 50
		    ],
	    ]);
	    
	    return RssView::widget([
		    'dataProvider' => $dataProvider,
		    'channel' => [
			    'title' => function ($widget, Feed $feed) {
				    $feed->addChannelTitle(Yii::$app->name);
			    },
			    'link' => Url::toRoute('/rss.xml', true),
			    'description' => 'Портал умного дома',
			    'language' => function ($widget, Feed $feed) {
				    return Yii::$app->language;
			    },
			    'image'=> function ($widget, Feed $feed) {
				    $feed->addChannelImage(Url::toRoute('/logo_120x120.png', true), Url::toRoute('/rss.xml', true), 120, 120, 'Sprut.ai');
			    },
		    ],
		    'items' => [
			    'title' => function ($model, $widget, Feed $feed) {
				    return $model->title;
			    },
			    'description' => function ($model, $widget, Feed $feed) {
				    return trim($model->descr);
			    },
			    'content' => function ($model, $widget, Feed $feed) {
	    	        $content = $model->text;
	    	        $content = str_replace('"/static', '"'.Url::home(true).'static', $content);
				    $content = str_replace('"/client', '"'.Url::home(true).'client', $content);
				    return $content;
			    },
			    'link' => function ($model, $widget, Feed $feed) {
				    return Url::toRoute(['client/'.$model->getUriModuleName().'/view', 'id' => $model->id], true);
			    },
			    'author' => function ($model, $widget, Feed $feed) {
				    return $model->author->email.' ('.$model->author->getAuthorName().')';
			    },
			    'guid' => function ($model, $widget, Feed $feed) {
				    $date = new \DateTime();
				    $date->setTimestamp($model->date_at);
				    return Url::toRoute(['client/'.$model->getUriModuleName().'/view', 'id' => $model->id], true);
			    },
			    'pubDate' => function ($model, $widget, Feed $feed) {
				    $date = new \DateTime();
				    $date->setTimestamp($model->date_at);
				    return $date->format(DATE_RSS);
			    },
			    'enclosure' => function ($model, $widget, Feed $feed) {
	    	        if ($model->image->getFileExists()) {
	    	        	$mediaModel = $model->image->getMediaImage();
	    	        	$url = Url::to($model->image->getImageSrc(1000, 400, \common\modules\media\helpers\enum\Mode::RESIZE, false), 'http');
	    	        	$size = $mediaModel->size;
	    	        	$type = 'image/'.$mediaModel->ext;
			            $feed->addItemEnclosure($url, $size, $type);
		            }
	    	        return null;
			    },
		    ],
	    ]);
    	
    }
    
    public function actionError() {
    	$url = '/client'.Yii::$app->request->url;
    	return $this->redirect($url, 301)->send();
    }
}
