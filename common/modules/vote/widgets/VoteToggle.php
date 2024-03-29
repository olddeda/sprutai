<?php
namespace common\modules\vote\widgets;

use Yii;
use yii\bootstrap\Html;

/**
 * @package common\modules\vote\widgets
 */
class VoteToggle extends BaseWidget
{
    /**
     * @var string
     */
    public $jsCodeKey = 'vote-toggle';

    /**
     * @var string
     */
    public $viewFile = 'toggle';

    /**
     * @var array
     */
    public $buttonOptions = [];

    /**
     * @return array
     */
    public function getDefaultOptions() {
        return [
            'class' => 'vote-toggle',
        ];
    }

    /**
     * @return array
     */
    public function getDefaultButtonOptions() {
        return [
            'class' => 'vote-btn btn btn-default',
            'icon' => Html::icon('glyphicon glyphicon-arrow-up'),
            'label' => Yii::t('vote', 'button_vote_up'),
        ];
    }

    public function init() {
        parent::init();
        $this->options = array_merge($this->getDefaultOptions(), $this->options);
        $this->buttonOptions = array_merge($this->getDefaultButtonOptions(), $this->buttonOptions);
        $this->initJsEvents($this->getSelector());
        $this->registerJs();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run() {
        return $this->render($this->viewFile, $this->getViewParams([
            'jsCodeKey' => $this->jsCodeKey,
            'entity' => $this->entity,
			'moduleType' => $this->moduleType,
            'model' => $this->model,
            'entityId' => $this->entityId,
            'userValue' => $this->userValue,
            'count' => isset($this->aggregateModel->positive) ? $this->aggregateModel->positive : 0,
            'options' => $this->options,
            'buttonOptions' => $this->buttonOptions,
        ]));
    }

    /**
     * Initialize with default events.
     *
     * @param string $selector
     */
    public function initJsEvents($selector) {
        if (!isset($this->jsBeforeVote)) {
            $this->jsBeforeVote = "
                $('$selector .vote-btn').prop('disabled', 'disabled').addClass('vote-loading');
                $('$selector .vote-btn').append('<div class=\"vote-loader\"><span></span><span></span><span></span></div>');
            ";
        }
        
        if (!isset($this->jsAfterVote)) {
            $this->jsAfterVote = "
                $('$selector .vote-btn').prop('disabled', false).removeClass('vote-loading');
                $('$selector .vote-btn .vote-loader').remove();
            ";
        }
        
        if (!isset($this->jsChangeCounters)) {
            $this->jsChangeCounters = "
                if (data.success) {
                    $('$selector .vote-count').text(data.aggregate.positive);
                    if (data.toggleValue) {
                        button.addClass('vote-active');
                    } else {
                        button.removeClass('vote-active');
                    }
                }
            ";
        }
    }
}
