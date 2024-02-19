<?php

namespace common\modules\rbac;

use Yii;
use yii\filters\AccessControl;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    /**
	 * @var bool Whether to show flash messages
	 */
    public $enableFlashMessages = true;

    /**
	 * @var string
	 */
    public $defaultRoute = 'role/index';
    
    /**
	 * @var array
	 */
    public $admins = [];

	/**
	 * @var array The rules to be used in URL management.
	 */
	public $urlRules = [
		'<controller:\w+>/<action:\w+>/<name:\w.+>' => '<controller>/<action>',
		'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
		'<controller:\w+>' => '<controller>',
	];
    
    /**
	 * @inheritdoc
	 */
    public function behaviors_() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->isSuperAdmin;
                        },
                    ]
                ],
            ],
        ];
    }

	/**
	 * @return null|static
	 */
	public static function module() {
		return static::getInstance();
	}
}