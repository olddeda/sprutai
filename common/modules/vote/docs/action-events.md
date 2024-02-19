# Action events

If you need to add or extend functionality (before or after vote action) you can attach action events.

1. Modify module config by adding your vote controller (`controllerMap`):

```php
 'modules' => [
    'vote' => [
        class' => common\modules\vote\Module::className(),
            'controllerMap' => [
                'default' => 'app\controllers\MyVoteController', // here
            ],
            'entities' => [
                'itemLike' => [
                    'modelName' => app\models\Item::className(),
                    'type' => common\modules\vote\Module::TYPE_TOGGLE,
                ],
                'itemFavorite' => [
                    'modelName' => app\models\Item::className(),
                    'type' => common\modules\vote\Module::TYPE_TOGGLE,
                ],
            ],
        ],
    ],
```

2. Add action `common\modules\vote\actions\VoteAction` to your controller:

```php
<?php

namespace app\controllers;

use common\modules\vote\actions\VoteAction;
use common\modules\vote\events\VoteActionEvent;
use yii\web\ForbiddenHttpException;
use yii\web\Controller;

class MyVoteController extends Controller
{
    public function actions()
    {
        return [
            'vote' => [
                'class' => VoteAction::className(),
                'on ' . VoteAction::EVENT_BEFORE_VOTE => function(VoteActionEvent $event) {
                    $event->responseData['before'] = microtime(true);
                    if (\Yii::$app->request->userIP == '192.168.0.23') {
                        throw new ForbiddenHttpException('You have no power here.');
                    }
                },
                'on ' . VoteAction::EVENT_AFTER_VOTE => function(VoteActionEvent $event) {
                    $event->responseData['after'] = microtime(true);
                    if ($event->voteForm->validate()) {
                        \Yii::$app->cache->delete('someCache');
                    }
                },
            ],
        ];
    }
}
```
