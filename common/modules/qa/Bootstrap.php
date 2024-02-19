<?php
namespace common\modules\qa;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;

use common\modules\qa\models\Answer;
use common\modules\qa\models\Question;

/**
 * Class Bootstrap
 * @package common\modules\qa
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        /** @var $module Module */
        if ($app->hasModule('qa') && ($module = $app->getModule('qa')) instanceof Module) {
            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'common\modules\qa\commands';
            } else {
                if (!class_exists($app->get('user')->identityClass)) {
                    throw new InvalidConfigException('Yii::$app->user->identityClass does not exist');
                }
            }
        }

        $app->i18n->translations[Module::TRANSLATION.'*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => __DIR__.'/messages',
            'fileMap' => [
                Module::TRANSLATION.'-main' => 'main.php',
                Module::TRANSLATION.'-model' => 'model.php'
            ]
        ];

        Yii::$container->set('common\modules\qa\models\AnswerInterface', Answer::class);
        Yii::$container->set('common\modules\qa\models\QuestionInterface', Question::class);
    }
}
