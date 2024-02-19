<?php
namespace api\modules\v1\controllers\content;

use api\modules\v1\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\components\youtube\Youtube;
use Yii;

/**
 * Class UtilsController
 * @package api\modules\v1\controllers\content
 */
class UtilsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/contents/utils/youtube-info",
     *     tags={"content-utils"},
     *     summary="Получение информации Youtube ролика",
     *     description="Возращает данные Youtube ролика по ссылке",
     *     operationId="content_utils_youtube_info",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/YoutubeInfo"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(ref="#/components/schemas/YoutubeInfo"),
     *         @OA\XmlContent(ref="#/components/schemas/YoutubeInfo")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     )
     * )
     */
    public function actionYoutubeInfo() {

        $url = Yii::$app->request->post('url');

        /** @var Youtube $youtube */
        $youtube = Yii::$app->youtube;

        return (array)$youtube->getVideoInfoUrl($url);
    }
}