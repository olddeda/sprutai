<?php
namespace common\modules\plugin\controllers;

use common\modules\base\components\ArrayHelper;
use common\modules\base\components\Controller;
use common\modules\base\extensions\editable\EditableAction;
use common\modules\content\helpers\enum\Status;
use common\modules\media\helpers\enum\Type;
use common\modules\plugin\helpers\enum\RepositoryProvider;
use common\modules\plugin\models\items\ItemRelease;
use common\modules\plugin\models\items\ItemRepository;
use common\modules\plugin\models\Plugin;
use common\modules\plugin\models\search\VersionSearch;
use common\modules\plugin\models\Version;
use common\modules\plugin\models\VersionRepository;
use common\modules\rbac\helpers\enum\Role;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * VersionController implements the CRUD actions for Version model.
 *
 * @property Plugin $pluginModel
 */
class VersionController extends Controller
{
    /**
     * @var integer
     */
    public $pluginId;

    /**
     * @var Version
     */
    public $pluginModel;
	
	/**
	 * @var array $urlParams
	 */
    public $urlParams = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $this->pluginId = Yii::$app->request->get('plugin_id', 0);
        $this->pluginModel = Plugin::findById($this->pluginId, true, 'plugin');
        
        $this->urlParams['plugin_id'] = $this->pluginId;
        if (Yii::$app->request->get('id'))
        	$this->urlParams['id'] = Yii::$app->request->get('id');

        return parent::beforeAction($action);
    }

	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Plugin::class,
			],
		]);
	}
	
	/**
	 * Lists all Version models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new VersionSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
            Version::tableName().'.plugin_id' => $this->pluginModel->id,
        ]);
		
		return $this->render('index', [
		    'plugin' => $this->pluginModel,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * Creates a new Version model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		
		/** @var Version $model */
		$model = $this->findModel();
		
		/** @var Version $latestVersion */
		$latestVersion = $this->pluginModel->version;
		if ($latestVersion) {
			if (is_null($model->repository->provider))
				$model->repository->provider = $latestVersion->repository->provider;
			if ($latestVersion->repository->provider == $model->repository->provider) {
				if (is_null($model->repository->owner))
					$model->repository->owner = $latestVersion->repository->owner;
				if (is_null($model->repository->token))
					$model->repository->token = $latestVersion->repository->token;
				if (is_null($model->repository->name))
					$model->repository->name = $latestVersion->repository->name;
			}
			$model->save(false);
		}
		
		if (is_null($model->repository->provider))
			return $this->redirect(ArrayHelper::merge(['version/select-provider'], $this->urlParams));
		
		if ($model->repository->provider == RepositoryProvider::GITHUB) {
			if (is_null($model->repository->token))
				return $this->redirect(ArrayHelper::merge(['version/authorize'], $this->urlParams));
			
			if (is_null($model->repository->name) || is_null($model->repository->owner))
				return $this->redirect(ArrayHelper::merge(['version/select-repository'], $this->urlParams));
			
			if (is_null($model->repository->tag))
				return $this->redirect(ArrayHelper::merge(['version/select-release'], $this->urlParams));
		}
		
		$model->status = Status::ENABLED;
		if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR]))
			$model->status = Status::MODERATED;

		// Validate and save
		if ($model->load(Yii::$app->request->post())) {
			if ($model->repository->provider == RepositoryProvider::MANUAL)
				$model->file = UploadedFile::getInstance($model, 'file');
			
			if ($model->validate() && $this->_saveArchive($model) && $model->save()) {
				
				// Reset latest
				Version::updateAll([
					'latest' => false
				], 'plugin_id = :plugin_id', [
					':plugin_id' => $this->pluginId,
				]);
				
				// Set new latest
				$model->latest = true;
				$model->save();
				
				// Set message
				Yii::$app->getSession()->setFlash('success', Yii::t('plugin-version', 'message_create_success'));
				
				// Redirect to view
				if (!$model->plugin->instruction)
					return $this->redirect(['/plugin/instruction/update', 'plugin_id' => $this->pluginId]);
				return $this->redirect(['/plugin/default/view', 'id' => $this->pluginId]);
			}
		}
		
		// Render view
		return $this->render('create', [
			'model' => $model,
			'plugin' => $this->pluginModel,
			'isCreate' => true,
		]);
	}
	
	/**
	 * Updates an existing Version model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionUpdate($id) {
		
		// Load model
		$model = $this->findModel($id, true);
		
		if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
			if (in_array($model->status, [Status::MODERATED, Status::ENABLED])) {
				throw new NotFoundHttpException(Yii::t('plugin-version', 'error_moderated'));
			}
			$model->status = Status::MODERATED;
		}
		
		// Validate and save
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
		 
			// Set message
            Yii::$app->getSession()->setFlash('success', Yii::t('plugin-version', 'message_update_success'));
            
            // Redirect to view
            return $this->redirect(['index', 'plugin_id' => $this->pluginId]);
		}
		
		// Render view
		return $this->render('update', [
			'model' => $model,
			'plugin' => $this->pluginModel,
			'isCreate' => false,
		]);
	}
	
	/**
	 * Deletes an existing Version model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
     *
     * @return \yii\web\Response
     */
	public function actionDelete($id) {
		
		// Find model and delete
		$this->findModel($id, true)->delete();
		
		// Set message
		Yii::$app->getSession()->setFlash('success', Yii::t('plugin-version', 'message_delete_success'));
		
		// Redirect to index
		return $this->redirect(['index', 'plugin_id' => $this->pluginId]);
	}
	
	/**
	 * Select provider
	 * @param bool $id
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionSelectProvider($id = false) {
		
		/** @var Version $model */
		$model = $this->findModel($id, true);
		
		if ($model->repository->load(Yii::$app->request->post())) {
			$model->repository->name = null;
			$model->repository->tag = null;
			
			$model->save(false);
			
			// Redirect
			$action = ($id) ? 'update' :'create';
			return $this->redirect(ArrayHelper::merge([$action], $this->urlParams));
		}
		
		// Render view
		return $this->render('select_provider', [
			'model' => $model,
			'plugin' => $this->pluginModel,
			'id' => $id,
		]);
	}
	
	/**
	 * Select repository
	 * @param bool $id
	 *
	 * @return string|\yii\web\Response
	 * @throws ErrorException
	 */
	public function actionSelectRepository($id = false) {
		
		/** @var Version $model */
		$model = $this->findModel($id, true);
		
		// Get repositories
		$repositories = $this->_getRepositories($model->repository);
		
		if ($model->repository->load(Yii::$app->request->post())) {
			$model->repository->tag = null;
			
			/** @var ItemRepository $item */
			$item = ArrayHelper::search($repositories, 'name', $model->repository->name, true);
			if ($item) {
				$model->repository->owner = $item->owner;
				$model->save(false);
			}
			
			// Redirect
			$action = ($id) ? 'update' :'create';
			return $this->redirect(ArrayHelper::merge([$action], $this->urlParams));
		}
		
		// Render view
		return $this->render('select_repository', [
			'model' => $model,
			'plugin' => $this->pluginModel,
			'id' => $id,
			'repositories' => $repositories,
		]);
	}
	
	/**
	 * Select release
	 * @param bool $id
	 *
	 * @return string|\yii\web\Response
	 * @throws ErrorException
	 */
	public function actionSelectRelease($id = false) {
		
		/** @var Version $model */
		$model = $this->findModel($id, true);
		
		/** @var ItemRelease[] $releases */
		$releases = $this->_getReleases($model->repository);
		
		/** @var ItemRelease $latest */
		$latest = $this->_getLatest($model->repository);
		if ($latest)
			$model->repository->tag = $latest->tag;
		
		if ($model->repository->load(Yii::$app->request->post())) {
			
			/** @var ItemRelease $item */
			$item = ArrayHelper::search($releases, 'tag', $model->repository->tag, true);
			if ($item) {
				
				$model->repository->tag = $item->tag;
				$model->repository->reference = $item->reference;
				$model->repository->created_at = strtotime($item->created_at);
				$model->repository->published_at = strtotime($item->published_at);
				
				$model->version = $item->tag;
				$model->text = $item->description;
				$model->save(false);
			}
			
			// Redirect
			$action = ($id) ? 'update' :'create';
			return $this->redirect(ArrayHelper::merge([$action], $this->urlParams));
		}
		
		$isExists = false;
		$disabledOptions = [];
		foreach ($this->pluginModel->versions as $version) {
			$disabledOptions[$version->repository->tag] = ['disabled' => true];
			if ($latest && $latest->tag == $version->repository->tag)
				$isExists = true;
		}
		
		
		// Render view
		return $this->render('select_release', [
			'model' => $model,
			'plugin' => $this->pluginModel,
			'id' => $id,
			'releases' => $releases,
			'disabledOptions' => $disabledOptions,
			'isExists' => $isExists,
		]);
	}
	
	/**
	 * Authorize selected provider
	 * @param bool $id
	 *
	 * @return \yii\web\Response
	 * @throws ErrorException
	 * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 */
	public function actionAuthorize($id = false) {
		
		/** @var Version $model */
		$model = $this->findModel($id, true);
		
		// Get token
		if (is_null($model->repository->token))
			return $this->_authorize($id, $model);
		
		// Redirect
		$action = ($id) ? 'update' :'create';
		return $this->redirect(ArrayHelper::merge([$action], $this->urlParams));
	}
	
	/**
	 * Finds the Version model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @param bool|false $own
	 *
	 * @return Plugin the loaded model
	 */
	protected function findModel($id = false, $own = false) {
		if ($id)
			return Version::findBy($id, true, 'plugin-version', [], false, $own);
		
		// Find or create temp model
		$model = Version::find()->where([
			'status' => Status::TEMP,
			'plugin_id' => $this->pluginId,
		])->one();
		if (is_null($model)) {
			$model = new Version();
			$model->status = Status::TEMP;
			$model->plugin_id = $this->pluginId;
			$model->save(false);
		}
		return $model;
	}
	
	/**
	 * @param bool $id
	 * @param Version $version
	 *
	 * @return \yii\web\Response
	 * @throws ErrorException
	 * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 */
	private function _authorize($id = false, Version $version) {
		$provider = $this->_getProvider($version->repository->provider);
		
		switch ($version->repository->provider) {
			case RepositoryProvider::GITHUB: {
				if (!isset($_GET['code'])) {
					
					// If we don't have an authorization code then get one
					$authUrl = $provider->getAuthorizationUrl([
						'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
						'scope' => ['repo'],
					]);
					$_SESSION['oauth2state'] = $provider->getState();

					return $this->redirect($authUrl);
				}
				
				else if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
					unset($_SESSION['oauth2state']);
					exit('Invalid state');
				}
				
				else {
					$token = $provider->getAccessToken('authorization_code', [
						'code' => $_GET['code']
					]);
					
					$version->repository->token = $token->getToken();
					$version->save(false);
					
					return $this->actionAuthorize($id);
				}
			}
			case RepositoryProvider::BITBUCKET: {
			
			}
			default: {
				throw new ErrorException('not setted authorized');
			}
		}
	}
	
	/**
	 * Get repositories
	 * @param VersionRepository $repository
	 *
	 * @return array
	 */
	private function _getRepositories(VersionRepository $repository) {
	
		switch ($repository->provider) {
			case RepositoryProvider::GITHUB: {
				$items = [];
				$repositories = $this->_createClientGithub($repository)->currentUser()->repositories();
				if (is_array($repositories)) {
					foreach ($repositories as $r) {
						$item = new ItemRepository();
						$item->name = $r['name'];
						$item->owner = $r['owner']['login'];
						$items[] = $item;
					}
				}
				return $items;
			}
			case RepositoryProvider::MANUAL: {
				return [];
			}
			case RepositoryProvider::URL: {
				return [];
			}
		}
		
		throw new ErrorException('not setted respository');
	}
	
	/**
	 * Get releases
	 * @param VersionRepository $repository
	 *
	 * @return array
	 */
	private function _getReleases(VersionRepository $repository) {
		switch ($repository->provider) {
			case RepositoryProvider::GITHUB: {
				$items = [];
				$releases = $this->_createClientGithub($repository)->api('repo')->releases()->all($repository->owner, $repository->name);
				if (is_array($releases)) {
					foreach ($releases as $r) {
						$item = new ItemRelease();
						$item->tag = $r['tag_name'];
						$item->description = $r['body'];
						$item->reference = 'refs/tags/'.$r['tag_name'];
						$item->created_at = $r['created_at'];
						$item->published_at = $r['published_at'];
						$items[] = $item;
					}
				}
				return $items;
			}
		}
		
		throw new ErrorException('not setted respository');
	}
	
	/**
	 * Get latest
	 * @param VersionRepository $repository
	 *
	 * @return ItemRelease
	 */
	private function _getLatest(VersionRepository $repository) {
		switch ($repository->provider) {
			case RepositoryProvider::GITHUB: {
				
				$releases = $this->_createClientGithub($repository)->api('repo')->releases()->all($repository->owner, $repository->name);
				if (count($releases)) {
					$latest = $this->_createClientGithub($repository)->api('repo')->releases()->latest($repository->owner, $repository->name);
					if ($latest) {
						$item = new ItemRelease();
						$item->tag = $latest['tag_name'];
						$item->description = $latest['body'];
						$item->reference = 'refs/tags/'.$latest['tag_name'];
						$item->created_at = $latest['created_at'];
						$item->published_at = $latest['published_at'];
						return $item;
					}
				}
				
				return null;
			}
		}
		
		throw new ErrorException('not setted respository');
	}
	
	/**
	 * Get provider
	 * @param int $provider
	 *
	 * @return \League\OAuth2\Client\Provider\Github
	 * @throws ErrorException
	 */
	private function _getProvider($provider) {
		switch ($provider) {
			case RepositoryProvider::GITHUB: {
				return new \League\OAuth2\Client\Provider\Github([
					'clientId' => 'b8ecf92948af3f3cac8b',
					'clientSecret' => 'bc62ece84c6012c06294fa4bc54a0656f3f765db',
					'redirectUri' => $this->_returnUrl(),
				]);
			}
			case RepositoryProvider::BITBUCKET: {
			}
		}
		
		throw new ErrorException('not setted provider');
	}
	
	/**
	 * Get archive
	 * @param VersionRepository $repository
	 * @param string $format
	 *
	 * @return mixed
	 * @throws ErrorException
	 */
	private function _getArchive(VersionRepository $repository, $format = 'tarball') {
		
		switch ($repository->provider) {
			case RepositoryProvider::GITHUB: {
				return $this->_createClientGithub($repository)->api('repo')->contents()->archive($repository->owner, $repository->name, $format, $repository->reference);
			}
		}
		
		throw new ErrorException('not setted respository');
	}
	
	/**
	 * Get return url
	 * @return string
	 */
	private function _returnUrl() {
		return Url::current([], true);
	}
	
	/**
	 * Create client for github
	 * @param VersionRepository $repository
	 *
	 * @return \Github\Client
	 */
	private function _createClientGithub(VersionRepository $repository) {
		
		// Create cache
		$cache = new \Redis();
		$cache->connect('127.0.0.1', 6379);
		
		// Create pull
		$pool = new \Cache\Adapter\Redis\RedisCachePool($cache);
		
		// Create client and get repositories
		$client = new \Github\Client();
		//$client->addCache($pool);
		$client->authenticate($repository->token, \Github\Client::AUTH_HTTP_TOKEN);
		
		return $client;
	}
	
	/**
	 * Save archive from reporistory to local
	 * @param Version $model
	 *
	 * @return bool
	 * @throws ErrorException
	 */
	private function _saveArchive(Version $model) {
		if ($model->repository->provider == RepositoryProvider::URL)
			return true;
		
		/** @var /common/modules/media/Module $module */
		$module = Yii::$app->getModule('media');
		
		/** @var $fs \creocoder\flysystem\LocalFilesystem $filesystem */
		$fs = $module->fs;
		
		$fileDir = $model->getFilePath();
		$fileName = $model->getFile();
		$filePath = $fileDir.$fileName;
		
		if (!$fs->has($fileDir))
			$fs->createDir($fileDir);
		
		if ($fs->has($filePath))
			$fs->delete($filePath);
		
		$contents = ($model->repository->provider ==  RepositoryProvider::MANUAL) ? file_get_contents($model->file->tempName) : $this->_getArchive($model->repository);
		
		$size = strlen($contents);
		$maxSize = $module->allowedMaxSize[Type::FILE];
		
		if ($size > $maxSize) {
			Yii::$app->getSession()->setFlash('danger', Yii::t('plugin-version', 'error_file_size', ['size' => Yii::$app->formatter->asShortSize($maxSize)]));
			return false;
		}
		
		$fs->write($filePath, $contents);
		
		return true;
	}
}