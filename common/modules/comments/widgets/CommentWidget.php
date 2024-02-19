<?php

namespace common\modules\comments\widgets;

use common\modules\base\helpers\enum\Status;
use common\modules\comments\models\Comment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Json;

use common\modules\comments\Module;
use common\modules\comments\CommentAsset;

/**
 * Class Comment
 * @package common\modules\comment\widgets
 */
class CommentWidget extends Widget
{
	/**
	 * @var module type
	 */
	public $moduleType;
	
	/**
	 * @var \yii\db\ActiveRecord|null Widget model
	 */
	public $model;

	/**
	 * @var string relatedTo custom text, for example: cms url: about-us, john comment about us page, etc.
	 * By default - className:primaryKey of the current model
	 */
	public $relatedTo = '';

	/**
	 * @var string the view file that will render the comment tree and form for posting comments.
	 */
	public $commentView = '@common/modules/comments/widgets/views/index';

	/**
	 * @var string comment form id
	 */
	public $formId = 'comment-form';

	/**
	 * @var null|integer maximum comments level, level starts from 1, null - unlimited level;
	 */
	public $maxLevel = null;

	/**
	 * @var string entity id attribute
	 */
	public $entityIdAttribute = 'id';

	/**
	 * @var array comment widget client options
	 */
	public $clientOptions = [];

	/**
	 * @var string hash(crc32) from class name of the widget model
	 */
	protected $entity;

	/**
	 * @var integer primary key value of the widget model
	 */
	protected $entityId;

	/**
	 * @var string encrypted entity key from params: entity, entityId, relatedTo
	 */
	protected $encryptedEntityKey;

	/**
	 * @var string pjax container id, generated automatically
	 */
	protected $pjaxContainerId;

	/**
	 * Initializes the widget params.
	 */
	public function init() {
		if (empty($this->model)) {
			throw new InvalidConfigException(Yii::t('comments', 'The "model" property must be set.'));
		}

		$this->pjaxContainerId = 'comment-pjax-container-'.$this->getId();
		$this->entity = hash('crc32', get_class($this->model));
		$this->entityId = $this->model->{$this->entityIdAttribute};

		if (empty($this->entityId)) {
			throw new InvalidConfigException(Yii::t('comments', 'The "entityIdAttribute" value for widget model cannot be empty.'));
		}

		if (empty($this->relatedTo)) {
			$this->relatedTo = get_class($this->model).':'.$this->entityId;
		}

		$this->encryptedEntityKey = Yii::$app->getSecurity()->encryptByKey(Json::encode([
			'entity' => $this->entity,
			'entity_id' => $this->entityId,
			'related_to' => $this->relatedTo
		]), Module::$name);
		
		$this->registerAssets();
	}

	/**
	 * Executes the widget.
	 * @return string the result of widget execution to be outputted.
	 * @throws InvalidConfigException
	 */
	public function run() {
		
		/* @var $module Module */
		$module = Yii::$app->getModule(Module::$name);
		
		$commentModelClass = $module->commentModelClass;
		
		$commentModel = Comment::find()->where([
			'module_type' => $this->moduleType,
			'entity' => $this->entity,
			'entity_id' => $this->entityId,
			'status' => Status::TEMP,
			'created_by' => Yii::$app->user->id,
		])->one();
		if (is_null($commentModel)) {
			$commentModel = Yii::createObject($commentModelClass);
			$commentModel->module_type = $this->moduleType;
			$commentModel->entity = $this->entity;
			$commentModel->entity_id = $this->entityId;
			$commentModel->status = Status::TEMP;
			$commentModel->save(false);
		}
		
		$comments = $commentModelClass::getTree($this->entity, $this->entityId, $this->maxLevel);

		return $this->render($this->commentView, [
			'comments' => $comments,
			'commentModel' => $commentModel,
			'maxLevel' => $this->maxLevel,
			'encryptedEntity' => $this->encryptedEntityKey,
			'pjaxContainerId' => $this->pjaxContainerId,
			'formId' => $this->formId
		]);
	}

	/**
	 * Register assets.
	 */
	protected function registerAssets() {
		$this->clientOptions['pjaxContainerId'] = '#'.$this->pjaxContainerId;
		$this->clientOptions['formSelector'] = '#'.$this->formId;
		$options = Json::encode($this->clientOptions);
		$view = $this->getView();

		CommentAsset::register($view);

		$view->registerJs("jQuery('#$this->formId').comment($options);");
	}

}
