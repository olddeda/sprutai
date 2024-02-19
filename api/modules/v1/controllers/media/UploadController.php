<?php
namespace api\modules\v1\controllers\media;

use api\modules\v1\components\Controller;

use api\modules\v1\controllers\media\actions\SlimAction;
use api\modules\v1\controllers\media\actions\DeleteAction;

/**
 * Class UploadController
 * @package api\modules\v1\controllers\media
 */
class UploadController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'slim' => [
                'class' => SlimAction::class,
            ],
            'delete' => [
                'class' => DeleteAction::class
            ]
        ];
    }
}