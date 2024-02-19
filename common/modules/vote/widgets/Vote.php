<?php
namespace common\modules\vote\widgets;

use Yii;

/**
 * @package common\modules\vote\widgets
 */
class Vote extends BaseWidget
{
    /**
     * @var string
     */
    public $jsCodeKey = 'vote';
    
    /** @var bool */
    public $hideCounters = false;

    /**
     * @return array
     */
    public function getDefaultOptions() {
        return [
            'class' => 'vote',
        ];
    }

    /**
     * @inherit
     */
    public function init() {
        parent::init();
        $this->options = array_merge($this->getDefaultOptions(), $this->options);
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
            'positive' => isset($this->aggregateModel->positive) ? $this->aggregateModel->positive : 0,
            'negative' => isset($this->aggregateModel->negative) ? $this->aggregateModel->negative : 0,
            'rating' => isset($this->aggregateModel->rating) ? $this->aggregateModel->rating : 0.0,
            'options' => $this->options,
			'hideCounters' => $this->hideCounters,
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
                $('$selector .vote-count')
                    .addClass('vote-loading')
                    .append('<div class=\"vote-loader\"><span></span><span></span><span></span></div>');
            ";
        }
        if (!isset($this->jsAfterVote)) {
            $this->jsAfterVote = "
                $('$selector .vote-btn').prop('disabled', false).removeClass('vote-loading');
                $('$selector .vote-count').removeClass('vote-loading').find('.vote-loader').remove();
            ";
        }
        if (!isset($this->jsChangeCounters)) {
            $this->jsChangeCounters = (!$this->hideCounters) ?  "
                if (data.success) {
                    $('$selector .vote-count span').text(data.aggregate.positive - data.aggregate.negative);
                    $('$selector .vote-count-up').text(data.aggregate.positive);
                    $('$selector .vote-count-down').text(data.aggregate.negative);
                    vote.find('button').removeClass('vote-active');
                    button.addClass('vote-active');
                }
            " : "
            	if (data.success) {
                    $('$selector .vote-count span').text('');
                    $('$selector .vote-count-up').text('');
                    $('$selector .vote-count-down').text('');
                    vote.find('button').removeClass('vote-active');
                    button.addClass('vote-active');
                }
            ";
        }
    }
}
