<?php
namespace api\modules\v1\controllers\dashboard;

use common\modules\base\components\Debug;
use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

//use api\modules\v1\components\Controller;
use common\modules\base\components\Controller;

use api\models\content\Content;
use common\modules\content\helpers\enum\Status as ContentStatus;

/**
 * Class ContentController
 * @package api\modules\v1\controllers\dashboard
 */
class ContentController extends Controller
{
	public function behaviors() {
		return [];
	}

    public function actionAliexpress() {
	    return Content::find()
            ->select('*')
            ->where(['in', 'status', [ContentStatus::DRAFT, ContentStatus::MODERATED, ContentStatus::ENABLED]])
            ->andWhere(['like', 'text', 'aliexpress.com/item'])
            ->orWhere(['like', 'text_new', 'aliexpress.com/item'])
            ->orderBy(['id' => SORT_ASC])->all();
    }

	public function actionNotOwnImage() {
	    $tmp = [];
        $query = Content::find()
            ->select('*')
            ->where(['in', 'status', [ContentStatus::DRAFT, ContentStatus::MODERATED, ContentStatus::ENABLED]])
            ->orderBy(['id' => SORT_ASC]);
        foreach ($query->batch(10) as $items) {
            foreach ($items as $item) {
                $search = [];
                preg_match('/<img.*?src="([^"]*)"[^>]*>(?:<img>)?/', $item->text, $search);
                if (
                    isset($search[1]) &&
                    strlen($search[1]) &&
                    strpos($search[1], 'static/media') === false &&
                    strpos($search[1], 'video.sprut.ai') === false &&
                    strpos($search[1], 'sprutmedia') === false &&
                    strpos($search[1], 'sprut.ai') === false
                ) {
                    $tmp[] = $item;
                }
            }
        }
        return $tmp;
    }
}
