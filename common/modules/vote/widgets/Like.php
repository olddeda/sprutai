<?php
namespace common\modules\vote\widgets;

use Yii;
use yii\bootstrap\Html;

/**
 * @package common\modules\vote\widgets
 */
class Like extends VoteToggle
{
    /**
     * @var string
     */
    public $jsCodeKey = 'vote-like';

    /**
     * @return array
     */
    public function getDefaultOptions() {
        return array_merge(parent::getDefaultOptions(), [
            'class' => 'vote-toggle vote-toggle-like',
        ]);
    }

    /**
     * @return array
     */
    public function getDefaultButtonOptions() {
        return array_merge(parent::getDefaultButtonOptions(), [
            'icon' => Html::icon('glyphicon glyphicon-heart'),
            'label' => Yii::t('vote', 'button_like'),
        ]);
    }
}
