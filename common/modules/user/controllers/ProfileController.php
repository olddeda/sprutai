<?php

namespace common\modules\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\user\Finder;

/**
 * ProfileController shows users profiles.
 *
 * @property \common\modules\user\Module $module
 */
class ProfileController extends Controller
{
    /** @var \common\modules\user\Finder */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param \common\modules\user\Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = []) {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index'],
						'roles' => ['@']
					],
					[
						'allow' => true,
						'actions' => ['view'],
						'roles' => ['?', '@']
					],
				],
			],
		]);
	}

    /**
     * Redirect to current user's profile.
     *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionIndex() {
		return $this->actionView(Yii::$app->user->getId());
    }
	
	/**
	 * Show user's profile.
	 *
	 * @param int $id
	 *
	 * @return \yii\web\Response
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionView($id) {
		$model = $this->finder->findUserById($id);
		
		if ($model === null)
			throw new NotFoundHttpException();
		
		// Render view
		return $this->render('view', [
			'model' => $model,
		]);
	}
}
