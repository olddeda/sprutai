<?php
namespace common\modules\vote\widgets;

use common\modules\base\components\Debug;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\web\JsExpression;
use yii\web\View;

use common\modules\vote\assets\VoteAsset;
use common\modules\vote\behaviors\VoteBehavior;
use common\modules\vote\models\VoteAggregate;
use common\modules\vote\traits\ModuleTrait;

/**
 * @package common\modules\vote\widgets
 */
abstract class BaseWidget extends Widget
{
    use ModuleTrait;
	
	/**
	 * @var integer
	 */
    public $moduleType;
    
    /**
     * @var string
     */
    public $entity;

    /**
     * @var null|\yii\db\ActiveRecord
     */
    public $model;

    /**
     * @var null|integer;
     */
    public $entityId;

    /**
     * @var string
     */
    public $voteUrl;

    /**
     * @var null|\common\modules\vote\models\VoteAggregate
     */
    public $aggregateModel;

    /**
     * @var null|integer
     */
    public $userValue;

    /**
     * @var string
     */
    public $jsBeforeVote;

    /**
     * @var string
     */
    public $jsAfterVote;

    /**
     * @var string
     */
    public $jsCodeKey = 'vote';

    /**
     * @var string
     */
    public $jsErrorVote;

    /**
     * @var string
     */
    public $jsShowMessage;

    /**
     * @var string
     */
    public $jsChangeCounters;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var string
     */
    public $viewFile = 'vote';

    /**
     * @var array
     */
    public $viewParams = [];

    /**
     * @var bool
     */
    protected $_behaviorIncluded;

    /**
     * @return string
     */
    public function getSelector() {
        $classes = str_replace(' ', '.', $this->options['class']);
        return ".{$classes}[data-entity=\"' + entity + '\"][data-entity-id=\"' + target  + '\"]";
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init() {
        parent::init();

        if (!isset($this->entity) || !isset($this->model)) {
            throw new InvalidArgumentException(Yii::t('vote', 'error_entity_and_model_must_be_set'));
        }

        $this->initDefaults();

        if ($this->getModule()->registerAsset) {
            $this->view->registerAssetBundle(VoteAsset::className());
        }
    }

    /**
     * Initialize widget with default options.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function initDefaults() {
        $this->voteUrl = isset($this->voteUrl) ?: Yii::$app->getUrlManager()->createUrl(['vote/default/vote']);
        $this->entityId = isset($this->entityId) ?: $this->model->getPrimaryKey();

        if (!isset($this->aggregateModel)) {
            $this->aggregateModel = $this->isBehaviorIncluded() ?
                $this->model->getVoteAggregate($this->entity) :
                VoteAggregate::findOne([
                    'entity' => $this->getModule()->encodeEntity($this->entity),
                    'entity_id' => $this->entityId,
                ]);
        }
        
        if (!isset($this->userValue)) {
            $this->userValue = $this->isBehaviorIncluded() ? $this->model->getUserValue($this->entity) : null;
        }
    }

    /**
     * Registers jQuery handler.
     */
    protected function registerJs() {
        $jsCode = new JsExpression("
            $('body').on('click', '[data-rel=\"{$this->jsCodeKey}\"] button', function(event) {
                var vote = $(this).closest('[data-rel=\"{$this->jsCodeKey}\"]'),
                    button = $(this),
                    action = button.attr('data-action'),
                    entity = vote.attr('data-entity'),
                    target = vote.attr('data-entity-id');
                jQuery.ajax({
                    url: '$this->voteUrl', type: 'POST', dataType: 'json', cache: false,
                    data: { 'VoteForm[entity]': entity, 'VoteForm[entityId]': target, 'VoteForm[action]': action },
                    beforeSend: function(jqXHR, settings) { $this->jsBeforeVote },
                    success: function(data, textStatus, jqXHR) { $this->jsChangeCounters $this->jsShowMessage },
                    complete: function(jqXHR, textStatus) { $this->jsAfterVote },
                    error: function(jqXHR, textStatus, errorThrown) { $this->jsErrorVote }
                });
            });
        ");
        $this->view->registerJs($jsCode, View::POS_END, $this->jsCodeKey);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function getViewParams(array $params) {
        return array_merge($this->viewParams, $params);
    }

    /**
     * @return bool
     */
    protected function isBehaviorIncluded() {
        if (isset($this->_behaviorIncluded)) {
            return $this->_behaviorIncluded;
        }

        if (!isset($this->aggregateModel) || !isset($this->userValue)) {
            foreach ($this->model->getBehaviors() as $behavior) {
                if ($behavior instanceof VoteBehavior) {
                    return $this->_behaviorIncluded = true;
                }
            }
        }

        return $this->_behaviorIncluded = false;
    }
}
