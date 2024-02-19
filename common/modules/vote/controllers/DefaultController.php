<?php
namespace common\modules\vote\controllers;

use Yii;
use yii\web\Controller;

use common\modules\vote\actions\VoteAction;

/**
 * @package common\modules\vote\controllers
 */
class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $defaultAction = 'vote';

    /**
     * @return array
     */
    public function actions() {
        return [
            'vote' => [
                'class' => VoteAction::class,
            ]
        ];
    }
}
